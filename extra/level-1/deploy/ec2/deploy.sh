#!/usr/bin/env bash
set -euo pipefail

export PATH="/usr/local/bin:$PATH"
export HOME=/root

REPO=/var/www/scalable-ecommerce
PREFIX=/scalable-ecommerce/prod

if [ ! -d "$REPO/.git" ]; then
  git clone https://github.com/yurgenlira/scalable-ecommerce.git "$REPO"
fi
git -C "$REPO" pull --ff-only

cd "$REPO/app"
composer install --no-dev --optimize-autoloader --no-interaction

{
  echo "APP_NAME=ScalableEcommerce"
  echo "APP_ENV=production"
  echo "APP_DEBUG=false"
  echo "LOG_CHANNEL=json"
  echo "APP_KEY=$(aws ssm get-parameter --name "$PREFIX/app_key" --with-decryption --query Parameter.Value --output text)"
  echo "DB_CONNECTION=pgsql"
  echo "DB_HOST=$(aws ssm get-parameter --name "$PREFIX/db_host" --query Parameter.Value --output text)"
  echo "DB_PORT=5432"
  echo "DB_DATABASE=$(aws ssm get-parameter --name "$PREFIX/db_name" --query Parameter.Value --output text)"
  echo "DB_USERNAME=$(aws ssm get-parameter --name "$PREFIX/db_username" --query Parameter.Value --output text)"
  echo "DB_PASSWORD=$(aws ssm get-parameter --name "$PREFIX/db_password" --with-decryption --query Parameter.Value --output text)"
} > .env

php artisan migrate --force
# seed on every deploy — ok for ephemeral infra + demo catalog (idempotent);
# move to a data migration / one-off seed once prod is persistent or the catalog is editable.
php artisan db:seed --force
php artisan config:cache
chown -R www-data:www-data storage bootstrap/cache
systemctl reload php8.5-fpm
# health-check the TLS vhost if a cert exists, else plain HTTP (no-domain mode)
# || true: missing dir = no-domain mode (expected)
domain=$(find /etc/letsencrypt/live -mindepth 1 -maxdepth 1 -type d -printf '%f\n' 2>/dev/null | head -1 || true)
if [ -n "$domain" ]; then
  curl -fsS --resolve "$domain:443:127.0.0.1" "https://$domain/up" > /dev/null
else
  curl -fsS http://localhost/up > /dev/null
fi
