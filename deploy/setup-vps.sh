#!/usr/bin/env bash
# ==============================================================================
# EcoTahu V1 - Ubuntu 22.04 / 24.04 LTS Production Auto-Setup Script
# Installs: Nginx, PHP 8.3-FPM, MySQL 8, Composer 2, Node.js 22, Certbot SSL,
#           UFW Firewall, Laravel Scheduler Cron, and deploys EcoTahu from Git.
# Usage:    sudo bash setup-vps.sh
# ==============================================================================

set -euo pipefail

if [ "${EUID:-$(id -u)}" -ne 0 ]; then
  echo "ERROR: Jalankan script ini sebagai root: sudo bash setup-vps.sh"
  exit 1
fi

APP_DIR="/var/www/eco-tahu"
DEFAULT_REPO="https://github.com/gsibyte-ops/eco-tahu.git"
DB_NAME="db_eco_tahu"
DB_USER="ecotahu_user"

echo "============================================================"
echo " EcoTahu V1 - VPS Ubuntu Auto-Deploy Setup"
echo "============================================================"

read -rp "Domain (mis. ecotahu.me) atau IP VPS jika belum ada domain: " DOMAIN
DOMAIN="$(echo "$DOMAIN" | xargs)"
if [ -z "$DOMAIN" ]; then
  echo "ERROR: Domain atau IP VPS wajib diisi."
  exit 1
fi

IS_IP=0
if [[ "$DOMAIN" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
  IS_IP=1
  APP_URL="http://${DOMAIN}"
  SSL_EMAIL=""
  echo "Mode IP terdeteksi -> APP_URL=${APP_URL} (SSL bisa dipasang nanti setelah domain diarahkan)."
else
  APP_URL="https://${DOMAIN}"
  read -rp "Email untuk SSL Let's Encrypt (mis. e41251254@student.polije.ac.id): " SSL_EMAIL
  SSL_EMAIL="$(echo "$SSL_EMAIL" | xargs)"
  if [ -z "$SSL_EMAIL" ]; then
    echo "ERROR: Email SSL wajib diisi untuk domain ${DOMAIN}."
    exit 1
  fi
fi

read -rp "Git Repo URL [${DEFAULT_REPO}]: " REPO_URL
REPO_URL="${REPO_URL:-$DEFAULT_REPO}"

read -rsp "Password baru untuk MySQL user '${DB_USER}' (kosongkan = auto-generate): " DB_PASS
echo ""
if [ -z "$DB_PASS" ]; then
  DB_PASS="$(openssl rand -base64 24 | tr -dc 'A-Za-z0-9' | head -c 24)"
  echo "Password DB dibuat otomatis (akan tersimpan di ${APP_DIR}/.env)."
fi

read -rp "Jalankan php artisan db:seed (data awal kategori, produk, resep, admin)? [y/N]: " RUN_SEED
RUN_SEED="${RUN_SEED:-N}"

export DEBIAN_FRONTEND=noninteractive

echo "[1/9] Update sistem & pasang paket dasar..."
apt-get update -y
apt-get install -y software-properties-common curl git unzip ca-certificates lsb-release gnupg ufw acl

echo "[2/9] Pasang PHP 8.3 & ekstensi Laravel 13..."
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y \
  nginx mysql-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-fileinfo

# Tuning PHP upload limit untuk foto/video produk & ulasan
cat > /etc/php/8.3/fpm/conf.d/99-ecotahu.ini <<'EOF'
upload_max_filesize = 25M
post_max_size = 30M
memory_limit = 256M
max_execution_time = 60
EOF
systemctl restart php8.3-fpm

echo "[3/9] Pasang Composer 2 & Node.js 22 LTS..."
if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi
if ! command -v node >/dev/null 2>&1 || [ "$(node -v | sed 's/^v//' | cut -d. -f1)" -lt 20 ]; then
  curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
  apt-get install -y nodejs
fi

echo "[4/9] Konfigurasi Firewall UFW (SSH, HTTP, HTTPS)..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

echo "[5/9] Setup Database MySQL 8 (${DB_NAME})..."
systemctl enable --now mysql
mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "[6/9] Clone / Update repo EcoTahu di ${APP_DIR}..."
mkdir -p "$(dirname "$APP_DIR")"
if [ -d "${APP_DIR}/.git" ]; then
  cd "$APP_DIR"
  git fetch --all
  git pull --ff-only
else
  git clone "$REPO_URL" "$APP_DIR"
  cd "$APP_DIR"
fi

echo "[7/9] Konfigurasi .env, install dependencies, build aset & migrasi DB..."
if [ ! -f "${APP_DIR}/.env" ]; then
  cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
fi

SESSION_SECURE="false"
if [ "$IS_IP" -eq 0 ]; then
  SESSION_SECURE="true"
fi

set_env() {
  local key="$1"
  local val="$2"
  if grep -qE "^#?\s*${key}=" "${APP_DIR}/.env"; then
    sed -i -E "s|^#?\s*${key}=.*|${key}=${val}|" "${APP_DIR}/.env"
  else
    echo "${key}=${val}" >> "${APP_DIR}/.env"
  fi
}

set_env "APP_NAME" "\"EcoTahu\""
set_env "APP_ENV" "production"
set_env "APP_DEBUG" "false"
set_env "APP_URL" "${APP_URL}"
set_env "APP_LOCALE" "id"
set_env "LOG_LEVEL" "warning"
set_env "DB_CONNECTION" "mysql"
set_env "DB_HOST" "127.0.0.1"
set_env "DB_PORT" "3306"
set_env "DB_DATABASE" "${DB_NAME}"
set_env "DB_USERNAME" "${DB_USER}"
set_env "DB_PASSWORD" "\"${DB_PASS}\""
set_env "SESSION_DRIVER" "database"
set_env "SESSION_SECURE_COOKIE" "${SESSION_SECURE}"
set_env "GOOGLE_REDIRECT_URI" "\"${APP_URL}/auth/google/callback\""

export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction

if ! grep -qE "^APP_KEY=base64:" "${APP_DIR}/.env"; then
  php artisan key:generate --force
fi

npm ci
npm run build

php artisan storage:link || true
php artisan migrate --force

if [[ "$RUN_SEED" =~ ^[Yy]$ ]]; then
  php artisan db:seed --force
fi

chown -R www-data:www-data "${APP_DIR}"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chmod 600 "${APP_DIR}/.env"

sudo -u www-data php artisan optimize

echo "[8/9] Konfigurasi Nginx & Cron Scheduler..."
cat > /etc/nginx/sites-available/ecotahu <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    index index.php;
    charset utf-8;
    client_max_body_size 30M;

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX

ln -sf /etc/nginx/sites-available/ecotahu /etc/nginx/sites-enabled/ecotahu
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now nginx
systemctl reload nginx

cat > /etc/cron.d/ecotahu-scheduler <<CRON
* * * * * www-data cd ${APP_DIR} && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
CRON
chmod 644 /etc/cron.d/ecotahu-scheduler

echo "[9/9] SSL HTTPS (Let's Encrypt)..."
if [ "$IS_IP" -eq 0 ]; then
  apt-get install -y certbot python3-certbot-nginx
  certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m "${SSL_EMAIL}" --redirect
  systemctl reload nginx
else
  echo "Lewati Certbot karena menggunakan IP. Nanti setelah domain diarahkan, jalankan:"
  echo "  sudo apt-get install -y certbot python3-certbot-nginx"
  echo "  sudo certbot --nginx -d domain-kamu.me --redirect"
fi

echo ""
echo "============================================================"
echo " DEPLOY SELESAI!"
echo " URL Web  : ${APP_URL}"
echo " API Base : ${APP_URL}/api"
echo " Health   : ${APP_URL}/up"
echo "============================================================"
echo "LANGKAH PENTING SELANJUTNYA:"
echo "1. Jika pakai Google Login, isi GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET di ${APP_DIR}/.env"
echo "   lalu jalankan: sudo -u www-data php ${APP_DIR}/artisan config:cache"
echo "2. Segera ganti password akun admin default (admin@ecotahu.id) dan hapus akun dummy test."
echo "3. Build APK production di laptop:"
echo "   flutter build apk --release --split-per-abi --dart-define=API_HOST=${APP_URL}"
