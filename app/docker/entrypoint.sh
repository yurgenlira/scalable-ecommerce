#!/bin/sh
set -e

if [ "$APP_ENV" = "production" ]; then
    php artisan optimize
fi

php-fpm -D
exec nginx -g 'daemon off;'
