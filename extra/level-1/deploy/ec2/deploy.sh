#!/usr/bin/env bash
set -euo pipefail

# SSM Run Command uses a restricted PATH that omits /usr/local/bin (where composer lives)
export PATH="/usr/local/bin:$PATH"

REPO=/var/www/scalable-ecommerce
PREFIX=/scalable-ecommerce/prod

# First deploy clones; later deploys fast-forward
if [ ! -d "$REPO/.git" ]; then
  git clone https://github.com/yurgenlira/scalable-ecommerce.git "$REPO"
fi
git -C "$REPO" pull --ff-only

cd "$REPO/app"
composer install --no-dev --optimize-autoloader --no-interaction

# Build .env from SSM Parameter Store
{
  echo "APP_NAME=ScalableEcommerce"
  echo "APP_ENV=production"
  echo "APP_DEBUG=false"
  echo "APP_KEY=$(aws ssm get-parameter --name "$PREFIX/app_key" --with-decryption --query Parameter.Value --output text)"
  echo "DB_CONNECTION=pgsql"
  echo "DB_HOST=$(aws ssm get-parameter --name "$PREFIX/db_host" --query Parameter.Value --output text)"
  echo "DB_PORT=5432"
  echo "DB_DATABASE=$(aws ssm get-parameter --name "$PREFIX/db_name" --query Parameter.Value --output text)"
  echo "DB_USERNAME=$(aws ssm get-parameter --name "$PREFIX/db_username" --query Parameter.Value --output text)"
  echo "DB_PASSWORD=$(aws ssm get-parameter --name "$PREFIX/db_password" --with-decryption --query Parameter.Value --output text)"
} > .env

php artisan migrate --force
php artisan config:cache
chown -R www-data:www-data storage bootstrap/cache
systemctl reload php8.5-fpm
curl -fsS http://localhost/up > /dev/null
