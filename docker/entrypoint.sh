#!/usr/bin/env bash
set -e

export PORT="${PORT:-10000}"

# Configure Apache to listen on Render's dynamic $PORT
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf || true
if ! grep -q "Listen ${PORT}" /etc/apache2/ports.conf; then
    echo "Listen ${PORT}" > /etc/apache2/ports.conf
fi
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Ensure runtime directories & permissions
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

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
    php artisan migrate --force || echo "[EcoTahu] Warning: migrate failed (check DB credentials/SSL)"

    # Auto-seed once if users table is empty (100% idempotent)
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

echo "[EcoTahu] Starting Apache on port ${PORT}..."
exec apache2-foreground
