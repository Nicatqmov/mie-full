#!/bin/bash
set -e

# Start PHP-FPM
php-fpm &

# Give PHP-FPM a moment
sleep 2

# Install dependencies & optimize Laravel
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (ignore failures)
php artisan migrate --force || true

# Ensure no default Nginx configs remain
rm -f /etc/nginx/sites-enabled/default \
      /etc/nginx/conf.d/default.conf

# Start Nginx in the foreground using the main config
nginx -g "daemon off;"
