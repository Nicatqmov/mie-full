FROM php:8.3-fpm

# Install system dependencies and PHP extensions (including pdo_pgsql)
RUN apt-get update && apt-get install -y \
    build-essential \
    nginx \
    curl \
    git \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libicu-dev \
  && docker-php-ext-install \
       pdo \
       pdo_mysql \
       pdo_pgsql \
       mbstring \
       exif \
       pcntl \
       bcmath \
       gd \
       zip \
       intl

RUN echo "max_execution_time = 120" > /usr/local/etc/php/conf.d/max_execution_time.ini

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Add Node.js and build front-end after copying
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Fix Laravel storage & cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Remove default Nginx configs
RUN rm -f /etc/nginx/sites-enabled/default \
        /etc/nginx/conf.d/default.conf

# Copy custom Nginx config
COPY ./conf/nginx/nginx-site.conf /etc/nginx/conf.d/laravel.conf

# Expose port
EXPOSE 80

# Copy & make start script executable
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Start
CMD ["bash", "/start.sh"]
