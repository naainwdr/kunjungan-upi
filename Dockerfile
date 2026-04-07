# ============================================================
# Dockerfile — Laravel 12, PHP 8.2 + Nginx + PHP-FPM
# Dioptimasi untuk Koyeb free tier (512MB RAM)
# ============================================================

# --- Stage 1: Install dependencies via Composer ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev

# --- Stage 2: Final image ---
FROM php:8.2-fpm-alpine

# Install Nginx + ekstensi PHP yang dibutuhkan
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        xml \
    && rm -rf /var/cache/apk/*

# Konfigurasi Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Konfigurasi PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Salin aplikasi dari stage vendor
COPY --from=vendor /app /var/www/html

# Buat folder yang diperlukan & set izin
RUN mkdir -p \
        storage/framework/{cache,sessions,views} \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8000

CMD ["/start.sh"]
