#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until php -r "new mysqli('${DB_HOST}', '${DB_USERNAME}', '${DB_PASSWORD}', '${DB_DATABASE}');" 2>/dev/null; do
  echo "MySQL is unavailable - sleeping"
  sleep 2
done

echo "MySQL is ready - executing migrations"
cd /var/www/html
php index.php migrate

echo "Starting Apache..."
exec apache2-foreground
