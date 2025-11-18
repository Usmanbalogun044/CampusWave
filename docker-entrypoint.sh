#!/usr/bin/env bash
set -e

# docker-entrypoint.sh
# Adjust Apache to listen on $PORT (Railway sets PORT), set permissions, run composer/artisan tasks.
# Environment variables you can set on Railway:
# - PORT (Railway sets automatically; default 8080)
# - APP_ENV (set to "production")
# - RUN_MIGRATIONS=true    (if you want the container to run migrations at startup)
# - RUN_SEEDERS=true       (if you want seeders run when RUN_MIGRATIONS=true)
# - APACHE_SERVER_NAME     (optional: override ServerName in Apache config)
# - Any Laravel .env vars (DB_*, APP_KEY, etc.)

: "${PORT:=8080}"
: "${APACHE_RUN_USER:=www-data}"
: "${APACHE_RUN_GROUP:=www-data}"
: "${APACHE_SERVER_NAME:=localhost}"

# Ensure a ServerName exists to suppress Apache startup warning about FQDN
if [ ! -f /etc/apache2/conf-available/servername.conf ]; then
  echo "ServerName ${APACHE_SERVER_NAME}" > /etc/apache2/conf-available/servername.conf || true
  a2enconf servername >/dev/null 2>&1 || true
fi

echo "=> Setting Apache to listen on port ${PORT}"
# Update ports.conf Listen directive
if grep -qE "Listen [0-9]+" /etc/apache2/ports.conf; then
  sed -ri "s/Listen [0-9]+/Listen ${PORT}/g" /etc/apache2/ports.conf
fi

# Update virtual host(s)
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
  sed -ri "s/<VirtualHost *:([0-9]+)>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf
fi

# Ensure runtime permissions
echo "=> Ensuring file permissions for ${APACHE_RUN_USER}:${APACHE_RUN_GROUP}"
chown -R ${APACHE_RUN_USER}:${APACHE_RUN_GROUP} /var/www/html || true
chmod -R 755 /var/www/html || true
mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
chown -R ${APACHE_RUN_USER}:${APACHE_RUN_GROUP} /var/www/html/storage /var/www/html/bootstrap/cache

# Move to app dir
cd /var/www/html || exit 1

# If vendor is missing (image was built without vendor or build cache changed), install dependencies at runtime.
if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  echo "=> Composer vendor not found, installing dependencies at runtime (no-dev, optimized)..."
  composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader || true
fi

# If Laravel app exists, perform safe runtime optimizations
if [ -f artisan ]; then
  # Generate APP_KEY if not set (will write to .env if present)
  if [ -z "${APP_KEY:-}" ]; then
    echo "=> APP_KEY not set, generating..."
    php artisan key:generate --force || true
  fi

  # Optionally run migrations (controlled by env var)
  if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "=> Running database migrations (force)..."
    php artisan migrate --force || true
    if [ "${RUN_SEEDERS:-false}" = "true" ]; then
      echo "=> Running seeders..."
      php artisan db:seed --force || true
    fi
  fi

  # Cache config/routes/views in production to increase performance
  if [ "${APP_ENV:-production}" = "production" ]; then
    echo "=> Caching config, routes and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
  fi
fi

# Exec the original command (apache2-foreground)
exec "$@"
