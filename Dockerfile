# ==============================================================================
# Stage 1: Build Frontend Assets (Vite + Tailwind CSS)
# ==============================================================================
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# ==============================================================================
# Stage 2: Production PHP 8.4 + Apache (Laravel 13)
# ==============================================================================
FROM php:8.4-apache

# Install system dependencies, CA certificates (for TiDB Cloud TLS), & PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && update-ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Recommended PHP production + OPcache tuning for 512MB container
RUN { \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'max_execution_time=60'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=96'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=0'; \
} > /usr/local/etc/php/conf.d/ecotahu.ini

# Configure Apache DocumentRoot -> /var/www/html/public
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy application source & compiled Vite assets
COPY . .
COPY --from=assets /app/public/build ./public/build

# Run package discovery now that full codebase is present
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi || true

# Prepare writable directories
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint (strip any Windows CRLF line endings cleanly)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
