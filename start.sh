#!/bin/bash

# Start PHP-FPM in background
php-fpm &

# Wait a moment to ensure PHP-FPM is up
sleep 2

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Laravel setup
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (optional, skip if not needed)
php artisan migrate --force || true

# Start NGINX in foreground
nginx -g "daemon off;"
