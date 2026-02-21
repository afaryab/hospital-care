#!/bin/sh

echo "Starting app container..."

# Set working directory
cd /var/www/html

# Ensure proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Run Laravel optimizations and migrations if artisan exists
if [ -f "/var/www/html/artisan" ]; then
    echo "Running Laravel optimizations..."
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    php artisan optimize:clear 2>/dev/null || true
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
