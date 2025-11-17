# Dockerfile for Laravel app (apache + php)
FROM php:8.2-apache

# Arguments
ARG user=www-data

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nano \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Enable mod_rewrite
RUN a2enmod rewrite

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader || true

# Set permissions
RUN chown -R ${user}:${user} /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80

CMD ["apache2-foreground"]
