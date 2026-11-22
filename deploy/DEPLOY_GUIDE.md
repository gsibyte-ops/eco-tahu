# Panduan Deploy EcoTahu V1 (VPS Ubuntu + Domain Gratis + APK Production)

Panduan singkat ini disiapkan untuk hari saat **GitHub Student Developer Pack** aktif (72 jam setelah approval).

---

## 1. Klaim VPS + Domain Gratis (GitHub Student Pack)

Buka https://education.github.com/pack/offers lewat akun GitHub Student (`ielvvv`):
1. **VPS Cloud (pilih salah satu):**
   - **Microsoft Azure for Students** ($100 credit tanpa kartu kredit) -> pilih **Sign in with GitHub** lewat **Microsoft Edge InPrivate** tanpa Adblock -> buat **Virtual Machine** `Ubuntu 24.04 LTS`, Region `Southeast Asia (Singapore)`, Size `Standard_B1s` / `B1ms`, buka port **22 (SSH), 80 (HTTP), 443 (HTTPS)** di tab Networking.
   - **Atau DigitalOcean** ($200 credit, verifikasi kartu debit Visa/Mastercard seperti Jago/Blu/Jenius/SeaBank) -> buat **Droplet** `Ubuntu 24.04 LTS`, Region `Singapore`, Basic Regular `$6/mo`.
2. **Domain Gratis 1 Tahun (pilih salah satu):**
   - **Namecheap** -> gratis 1 tahun `.me` (mis. `ecotahu.me`), atau
   - **Name.com** -> gratis 1 tahun `.live` / `.app` / `.studio`.
3. **Arahkan DNS Domain ke IP VPS:**
   - Di menu **DNS Management** domain (Namecheap / Name.com), buat **A Record**:
     - `Host / Name`: `@`
     - `Value / IP Address`: `<IP_PUBLIC_VPS_KAMU>`
     - `TTL`: Automatic / 300
   - Tunggu 2-10 menit sampai `ping domain-kamu.me` mengarah ke IP VPS.

---

## 2. Jalankan Auto-Deploy di VPS (1 Perintah)

Masuk ke VPS lewat PowerShell di Windows:
```powershell
ssh root@<IP_VPS_KAMU>
# (Untuk Azure Ubuntu biasanya: ssh azureuser@<IP_VPS_KAMU>)
```

Di dalam terminal VPS, jalankan:
```bash
curl -fsSL https://raw.githubusercontent.com/gsibyte-ops/eco-tahu/main/deploy/setup-vps.sh -o setup-vps.sh
sudo bash setup-vps.sh
```

Script otomatis memasang **Nginx + PHP 8.3-FPM + MySQL 8 + Composer + Node 22 + Cron Scheduler + SSL HTTPS Let's Encrypt**.

### Setelah Update Kode di Masa Depan
Setiap kali abang `git push` ke GitHub, cukup jalankan di VPS:
```bash
sudo bash /var/www/eco-tahu/deploy/update-vps.sh
```

---

## 3. Setting Pasca-Deploy (Wajib)

1. **Ganti Password Admin Default & Hapus Akun Dummy:**
   - Login ke `https://domain-kamu.me/login` lalu ganti password akun `admin@ecotahu.id` (dan hapus user dummy `budi@test.com`, `rina@test.com`, `siti@test.com`, `andi@test.com` jika tidak diperlukan).
2. **Google OAuth (Web + Mobile):**
   - Di **Google Cloud Console -> Credentials**, **rotate/buat ulang Client Secret** (karena secret lama sempat ada di `.env.example` dan sekarang sudah dibersihkan), tambahkan **Authorized redirect URI**: `https://domain-kamu.me/auth/google/callback`.
   - Masukkan `GOOGLE_CLIENT_ID` & `GOOGLE_CLIENT_SECRET` ke `/var/www/eco-tahu/.env`, lalu jalankan:
     ```bash
     sudo -u www-data php /var/www/eco-tahu/artisan config:cache
     ```
   - Di **Firebase Console -> Project Settings -> Android App**, tambahkan **SHA-1 & SHA-256** dari release keystore (`android/app/ecotahu-release.jks`), lalu unduh ulang `google-services.json` ke `eco-tahu-mobile/android/app/google-services.json`.

---

## 4. Build APK Production (Tersambung ke Web Live)

Di PowerShell laptop (`d:\laragon\www\eco-tahu-mobile`), jalankan:
```powershell
flutter build apk --release --split-per-abi --dart-define=API_HOST=https://domain-kamu.me
```

File APK siap sebar (sudah ditandatangani dengan release keystore) akan keluar di:
- `build\app\outputs\flutter-apk\app-arm64-v8a-release.apk` (untuk 95% HP Android modern)
- `build\app\outputs\flutter-apk\app-armeabi-v7a-release.apk` (untuk HP Android 32-bit lama)

Untuk upload ke **Google Play Store** nanti, cukup jalankan:
```powershell
flutter build appbundle --release --dart-define=API_HOST=https://domain-kamu.me
```
