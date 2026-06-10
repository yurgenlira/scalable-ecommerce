#!/bin/sh
set -e

php artisan optimize

php-fpm -D
exec nginx -g 'daemon off;'
