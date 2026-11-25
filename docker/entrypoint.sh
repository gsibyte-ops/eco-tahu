#!/usr/bin/env bash
set -e

export PORT="${PORT:-7860}"
mkdir -p /tmp/apache2 storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs storage/app/public bootstrap/cache

# If host specifies a non-7860 PORT, update Apache config cleanly (writable by uid=1000)
if [ "${PORT}" != "7860" ]; then
    echo "Listen ${PORT}" > /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf
fi

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
