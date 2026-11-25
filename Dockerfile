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
# Stage 2: Production PHP 8.4 + Apache (Laravel 13, non-root uid=1000 HF-ready)
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

# Create non-root user (uid=1000) required by Hugging Face Spaces
RUN useradd -m -u 1000 user

# PHP production + OPcache config
RUN { \
    echo 'memory_limit=512M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'max_execution_time=60'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=0'; \
} > /usr/local/etc/php/conf.d/ecotahu.ini

# Configure Apache for non-root port 7860 (HF Spaces default)
ENV PORT=7860
ENV APACHE_RUN_USER=user
ENV APACHE_RUN_GROUP=user
ENV APACHE_PID_FILE=/tmp/apache2.pid
ENV APACHE_RUN_DIR=/tmp/apache2
ENV APACHE_LOCK_DIR=/tmp/apache2
ENV APACHE_LOG_DIR=/tmp/apache2

RUN echo "Listen 7860" > /etc/apache2/ports.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf
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

# Entrypoint (strip any Windows CRLF line endings cleanly)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Prepare writable directories for non-root uid=1000
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs storage/app/public bootstrap/cache /tmp/apache2 \
    && chown -R user:user /var/www/html /tmp/apache2 /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf \
    && chmod -R 775 storage bootstrap/cache /tmp/apache2

USER user

EXPOSE 7860

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
