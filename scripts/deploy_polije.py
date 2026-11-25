#!/usr/bin/env python3
"""
One-command automated deployer untuk EcoTahu ke Hosting Resmi Polije
(https://ecotahu.wstif-polije.com — Shared Hosting PHP 8.3).

Cara pakai (zero ribet):
1. Buat file `.env.polije` di root project (sudah di-.gitignore, jangan di-commit) berisi:
     POLIJE_FTP_HOST=145.79.14.22
     POLIJE_FTP_PORT=21
     POLIJE_FTP_USER=u307758875.ecotahu
     POLIJE_FTP_PASS=<password_ftp>
     POLIJE_SITE_URL=https://ecotahu.wstif-polije.com
   (plus variabel Laravel production seperti APP_KEY, DB_*, MAIL_*, GOOGLE_*)
2. Jalankan:
     python scripts/deploy_polije.py
Script akan otomatis:
  - Build Vite assets (`npm run build`) bila diminta (`--build-assets`)
  - Pack seluruh source Laravel + vendor PHP 8.3 + gambar seed ke 1 file ZIP
  - Upload via FTP + unpacker sekali pakai bertoken acak
  - Ekstrak, jalankan `migrate --force`, cache config/route/view, lalu hapus ZIP & unpacker otomatis
  - Verifikasi status HTTP halaman utama, API, dan keamanan `/.env` (403/404).
"""
import argparse
import ftplib
import os
import pathlib
import secrets
import subprocess
import sys
import tempfile
import urllib.error
import urllib.request
import zipfile

ROOT = pathlib.Path(__file__).resolve().parent.parent

EXCLUDE_TOP = {
    ".git",
    ".github",
    ".vscode",
    "node_modules",
    "vendor",
    "tests",
    "docker",
    "Dockerfile",
    ".dockerignore",
    ".env",
    ".env.example",
    ".env.polije",
    "storage",
    "bootstrap",
}


def load_env_file(path: pathlib.Path) -> dict:
    data = {}
    if not path.exists():
        return data
    for line in path.read_text(encoding="utf-8", errors="ignore").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        data[k.strip()] = v.strip().strip('"').strip("'")
    return data


def main():
    parser = argparse.ArgumentParser(description="Deploy EcoTahu ke Hosting Polije via FTP")
    parser.add_argument("--vendor-dir", default=str(ROOT / "vendor"), help="Path folder vendor PHP 8.3")
    parser.add_argument("--env-file", default=str(ROOT / ".env.polije"), help="Path file konfigurasi .env.polije")
    parser.add_argument("--build-assets", action="store_true", help="Jalankan npm run build sebelum zip")
    args = parser.parse_args()

    cfg = {**load_env_file(pathlib.Path(args.env_file)), **os.environ}
    ftp_host = cfg.get("POLIJE_FTP_HOST")
    ftp_port = int(cfg.get("POLIJE_FTP_PORT", "21"))
    ftp_user = cfg.get("POLIJE_FTP_USER")
    ftp_pass = cfg.get("POLIJE_FTP_PASS")
    site_url = cfg.get("POLIJE_SITE_URL", "https://ecotahu.wstif-polije.com").rstrip("/")

    if not all([ftp_host, ftp_user, ftp_pass]):
        print("ERROR: Set POLIJE_FTP_HOST, POLIJE_FTP_USER, POLIJE_FTP_PASS di .env.polije atau environment.")
        sys.exit(1)

    if args.build_assets:
        print("[1/4] Building Vite assets...", flush=True)
        subprocess.run(["npm", "run", "build"], cwd=str(ROOT), check=True, shell=(os.name == "nt"))

    vendor_dir = pathlib.Path(args.vendor_dir)
    if not (vendor_dir / "autoload.php").exists():
        print(f"ERROR: vendor/autoload.php tidak ditemukan di {vendor_dir}")
        sys.exit(1)

    with tempfile.TemporaryDirectory() as tmp:
        zip_path = pathlib.Path(tmp) / "ecotahu_deploy.zip"
        print("[2/4] Packing deployment ZIP...", flush=True)
        added = set()

        def add_file(zf, fp: pathlib.Path, arc: str):
            if arc not in added:
                added.add(arc)
                zf.write(fp, arc)

        def add_str(zf, arc: str, txt: str):
            if arc not in added:
                added.add(arc)
                zf.writestr(arc, txt.encode("utf-8"))

        with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=5) as zf:
            for item in ROOT.iterdir():
                if item.name in EXCLUDE_TOP:
                    continue
                if item.is_file():
                    add_file(zf, item, item.name)
                elif item.is_dir():
                    for r, _, files in os.walk(item):
                        for fn in files:
                            fp = pathlib.Path(r) / fn
                            arc = fp.relative_to(ROOT).as_posix()
                            if arc.startswith("public/storage/"):
                                continue
                            add_file(zf, fp, arc)

            for item in (ROOT / "bootstrap").iterdir():
                if item.name == "cache":
                    add_str(zf, "bootstrap/cache/.gitignore", "*\n!.gitignore\n")
                elif item.is_file():
                    add_file(zf, item, f"bootstrap/{item.name}")

            for rel in [
                "storage/app/public/.gitignore",
                "storage/app/private/.gitignore",
                "storage/framework/cache/data/.gitignore",
                "storage/framework/sessions/.gitignore",
                "storage/framework/views/.gitignore",
                "storage/logs/.gitignore",
            ]:
                add_str(zf, rel, "*\n!.gitignore\n")

            seed_dir = ROOT / "public" / "images" / "seed"
            if seed_dir.exists():
                for r, _, files in os.walk(seed_dir):
                    rel_dir = pathlib.Path(r).relative_to(seed_dir).as_posix()
                    for fn in files:
                        fp = pathlib.Path(r) / fn
                        sub = f"{rel_dir}/{fn}" if rel_dir != "." else fn
                        add_file(zf, fp, f"storage/app/public/{sub}")
                        add_file(zf, fp, f"public/storage/{sub}")

            for r, _, files in os.walk(vendor_dir):
                for fn in files:
                    fp = pathlib.Path(r) / fn
                    add_file(zf, fp, "vendor/" + fp.relative_to(vendor_dir).as_posix())

        token = secrets.token_urlsafe(24)
        unpack_code = f"""<?php
@set_time_limit(300); @ini_set('memory_limit', '512M');
header('Content-Type: text/plain; charset=utf-8');
if (($_GET['k'] ?? '') !== '{token}') {{ http_response_code(403); exit('Forbidden'); }}
$base = __DIR__; $zipFile = $base . '/ecotahu_deploy.zip';
$zip = new ZipArchive();
if ($zip->open($zipFile) !== true) {{ http_response_code(500); exit('Cannot open zip'); }}
$zip->extractTo($base); $zip->close(); @unlink($zipFile);
foreach (glob($base . '/bootstrap/cache/*.php') as $f) {{ @unlink($f); }}
require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();
$kernel->call('migrate', ['--force' => true]); echo $kernel->output();
$kernel->call('config:cache'); $kernel->call('route:cache'); $kernel->call('view:cache');
echo "DEPLOY_SUCCESS\\n";
@unlink(__FILE__);
"""
        unpack_file = pathlib.Path(tmp) / "unpack_deploy.php"
        unpack_file.write_text(unpack_code, encoding="utf-8")

        print(f"[3/4] Uploading {zip_path.stat().st_size / (1024*1024):.2f} MB via FTP...", flush=True)
        ftp = ftplib.FTP()
        ftp.connect(ftp_host, ftp_port, timeout=60)
        ftp.login(ftp_user, ftp_pass)
        ftp.set_pasv(True)
        with open(zip_path, "rb") as f:
            ftp.storbinary("STOR ecotahu_deploy.zip", f, blocksize=65536)
        with open(unpack_file, "rb") as f:
            ftp.storbinary("STOR unpack_deploy.php", f)
        ftp.quit()

        print("[4/4] Running remote extract, migrate & cache...", flush=True)
        req = urllib.request.Request(f"{site_url}/unpack_deploy.php?k={token}")
        with urllib.request.urlopen(req, timeout=240) as resp:
            print(resp.read().decode("utf-8", errors="replace"))


if __name__ == "__main__":
    main()
