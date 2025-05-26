#!/bin/bash

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Running Laravel setup..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force || true

# Start default NGINX + PHP-FPM
/start.sh
