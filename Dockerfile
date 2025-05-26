# Stage 1: Build PHP app with composer
FROM php:8.2-fpm AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip pgsql pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy all files to container
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear caches and optimize
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Stage 2: Setup nginx + php-fpm runtime
FROM nginx:alpine

COPY --from=builder /var/www/html /var/www/html

# Copy nginx config file (you need to create this file, see below)
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# Set permissions (optional)
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
