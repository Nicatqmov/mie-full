#!/bin/bash
set -e

# Start PHP-FPM in background
php-fpm &

# Give PHP-FPM a moment to initialize
sleep 2

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Laravel optimizations
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations and seeders (force mode for production)
# php artisan migrate

# Clean default nginx configs (optional depending on your base image)
rm -f /etc/nginx/sites-enabled/default \
      /etc/nginx/conf.d/default.conf

# Start queue worker in the background (logs errors & restarts on failure)
php artisan queue:work &

# Start Nginx in foreground
nginx -g "daemon off;"
