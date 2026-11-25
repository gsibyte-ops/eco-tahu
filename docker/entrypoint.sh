#!/usr/bin/env bash
set -e

export PORT="${PORT:-7860}"

# Update Nginx listen port if container PORT differs from 7860
if [ "${PORT}" != "7860" ]; then
    sed -i "s/listen 7860/listen ${PORT}/g" /etc/nginx/sites-enabled/ecotahu.conf
    sed -i "s/listen \[::\]:7860/listen [::]:${PORT}/g" /etc/nginx/sites-enabled/ecotahu.conf
fi

# Ensure runtime directories & sync seeded catalog/article images into storage/app/public
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs storage/app/public bootstrap/cache
if [ -d "public/images/seed" ]; then
    cp -rn public/images/seed/. storage/app/public/ || true
fi
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Symlink public storage
php artisan storage:link --force || true

# Cache Laravel config, routes, and views for fast production responses
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run database migrations automatically (when DB_HOST is configured)
if [ -n "${DB_HOST:-}" ]; then
    echo "[EcoTahu] Running database migrations..."
    php artisan migrate --force || echo "[EcoTahu] Warning: migrate failed"

    USER_COUNT=$(php -r '
        require "vendor/autoload.php";
        $app = require "bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        try {
            echo \Illuminate\Support\Facades\DB::table("users")->count();
        } catch (\Throwable $e) {
            echo "-1";
        }
    ' 2>/dev/null || echo "-1")

    if [ "$USER_COUNT" = "0" ]; then
        echo "[EcoTahu] Fresh database detected (0 users). Running DatabaseSeeder..."
        php artisan db:seed --force || echo "[EcoTahu] Warning: db:seed failed"
    else
        echo "[EcoTahu] Skipping seeder (users count: ${USER_COUNT})."
    fi
fi

echo "[EcoTahu] Starting PHP-FPM and Nginx on port ${PORT}..."
php-fpm -D
exec nginx -g "daemon off;"
