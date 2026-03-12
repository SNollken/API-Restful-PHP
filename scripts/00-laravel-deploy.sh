#!/usr/bin/env bash
set -e

cd /var/www/html

chmod -R ug+rwx storage bootstrap/cache

if [ ! -d vendor ]; then
  composer install --no-dev --optimize-autoloader --no-interaction
fi

php artisan config:cache
php artisan route:cache

php artisan migrate --force
