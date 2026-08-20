#!/bin/sh

echo "Starting unified Laravel container..."

# Set working directory
cd /var/www/html

# Ensure proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# DMS zip build/extract scratch space — this container runs the queue
# worker that processes BuildFolderZipJob/SafeExtractZipJob.
mkdir -p /var/www/html/storage/app/private/dms-tmp

# Run Laravel optimizations and migrations if artisan exists. Failures are
# intentionally non-fatal (a broken cache shouldn't stop the container from
# starting), but they must stay visible — previously stderr and the exit
# code were both suppressed, so a broken route:cache (e.g. a route file
# syntax error) would boot the container with a stale or missing route
# cache and leave zero trace in the logs. optimize:clear also now runs
# *before* the cache builds, not after — it was previously wiping out the
# caches this same block had just built two lines earlier.
if [ -f "/var/www/html/artisan" ]; then
    echo "Running Laravel optimizations..."
    php artisan optimize:clear || echo "WARNING: optimize:clear failed (exit $?)."
    php artisan config:cache || echo "WARNING: config:cache failed (exit $?) — app may run without a fresh config cache."
    php artisan route:cache || echo "WARNING: route:cache failed (exit $?) — app may run with a stale or missing route cache."
    php artisan view:cache || echo "WARNING: view:cache failed (exit $?) — views will compile on demand instead of being precompiled."

    echo "Waiting for database and applying migrations..."
    tries=0
    max_tries=40 # ~2 minutes
    until php artisan migrate --seed --force; do
        tries=$((tries+1))
        if [ "$tries" -ge "$max_tries" ]; then
            echo "Database not ready after $((3*max_tries))s; continuing without migrations."
            break
        fi
        echo "Database not ready, retrying in 3s... ($tries/$max_tries)"
        sleep 3
    done
fi


# Create log directories for supervisor
mkdir -p /var/log/supervisor

# Check if supervisord config exists
if [ ! -f "/etc/supervisor/conf.d/supervisord.conf" ]; then
    echo "ERROR: supervisord.conf not found!"
    exit 1
fi

# Adjust folder permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Start supervisor which will manage nginx, php-fpm, ssh, workers, and cron
echo "Starting supervisor with all services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
