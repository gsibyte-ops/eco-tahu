#!/usr/bin/env bash
# ==============================================================================
# EcoTahu V1 - Fast Zero-Hassle VPS Update Script
# Usage: sudo bash /var/www/eco-tahu/deploy/update-vps.sh
# ==============================================================================

set -euo pipefail

APP_DIR="/var/www/eco-tahu"
cd "$APP_DIR"

echo "[1/5] Pull perubahan terbaru dari GitHub..."
git pull --ff-only

echo "[2/5] Install dependencies PHP & build aset Vite..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

echo "[3/5] Jalankan migrasi database..."
php artisan migrate --force

echo "[4/5] Perbarui permission & cache production..."
chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" "${APP_DIR}/public/build"
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan optimize

echo "[5/5] Reload PHP-FPM & Nginx..."
systemctl reload php8.3-fpm
systemctl reload nginx

echo "Update EcoTahu selesai!"
