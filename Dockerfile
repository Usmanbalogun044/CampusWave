# Production-ready Dockerfile for Laravel on Railway (apache + php)
# Notes:
# - Railway provides the PORT env var; we update Apache at container start to listen on $PORT.
# - Composer install is done during build (no-dev, optimized). Final image copies app files.
# - An entrypoint script will perform final, safe runtime tasks (permissions, optional migrations, artisan caches).
FROM php:8.2-apache

# Build args / defaults
ARG user=www-data
ENV APACHE_RUN_USER=${user} \
    APACHE_RUN_GROUP=${user} \
    APP_ENV=production \
    PORT=8080

# Install system dependencies and PHP extensions required by Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nano \
    procps \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable useful Apache modules
RUN a2enmod rewrite headers expires

# Suppress "Could not reliably determine the server's fully qualified domain name" by setting ServerName
RUN printf "ServerName localhost\n" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername || true

# Install composer (copy from official composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer.json first to leverage Docker layer cache (do not require composer.lock)
COPY composer.json ./

# Install PHP dependencies for production (no dev)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts --classmap-authoritative --no-progress || true

# Copy application code
COPY . /var/www/html

# Ensure directories exist and correct permissions for runtime
RUN chown -R ${user}:${user} /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R ${user}:${user} /var/www/html/storage /var/www/html/bootstrap/cache

# Add entrypoint script (will adjust Apache port and run final runtime tasks)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose default Railway port (Railway will set PORT env var)
EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
