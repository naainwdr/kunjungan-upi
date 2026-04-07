#!/bin/sh
set -e

cd /var/www/html

echo "=== Menjalankan migrasi database ==="
php artisan migrate --force

echo "=== Membuat akun admin ==="
php artisan db:seed --class=AdminUserSeeder --force

echo "=== Caching konfigurasi, routes, views ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Menyiapkan storage link ==="
php artisan storage:link || true

echo "=== Menjalankan PHP-FPM di background ==="
php-fpm -D

echo "=== Menjalankan Nginx di port ${PORT:-8000} ==="
# Ganti port default 8000 dengan $PORT jika ada
sed -i "s/listen 8000/listen ${PORT:-8000}/g" /etc/nginx/nginx.conf

exec nginx -g "daemon off;"
