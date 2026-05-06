#!/bin/bash
set -e

cd /var/www/html

if [ -d "/var/www/html/storage" ]; then
  chown -R www-data:www-data /var/www/html/storage
fi

# На всякий случай мало-ли
echo "🔄 Waiting for MySQL at mysql:3306..."

# Ждём, пока порт не станет доступен
while ! nc -z "mysql" "3306"; do
  echo "⏳ MySQL not ready, retrying in 1s..."
  sleep 1
done

echo "✅ MySQL is ready!"

exec "$@"