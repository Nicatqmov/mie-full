#!/bin/bash
set -e

# Start PHP-FPM in background
php-fpm &

# Give PHP-FPM a moment
sleep 2

# Install PHP dependencies
composer install --no-dev --optimize-autoloader


# Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (ignore failures)
php artisan migrate --force || true

# Clean default nginx configs
rm -f /etc/nginx/sites-enabled/default \
      /etc/nginx/conf.d/default.conf

# Start Nginx in foreground
nginx -g "daemon off;"
