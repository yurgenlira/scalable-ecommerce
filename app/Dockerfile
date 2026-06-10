# syntax=docker/dockerfile:1

# ----------------------------------------------------------
# Stage: composer-deps (production dependencies)
# ----------------------------------------------------------
FROM composer:2.9.7 AS composer-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader \
    --prefer-dist --no-interaction --no-progress
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# ----------------------------------------------------------
# Stage: base (shared runtime environment)
# ----------------------------------------------------------
FROM php:8.5.7-fpm-alpine3.24 AS base

RUN apk add --no-cache nginx curl libpq \
    && apk add --no-cache --virtual .build-deps postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && apk del .build-deps \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx \
    && mkdir -p /run/nginx && chown www-data:www-data /run/nginx

WORKDIR /var/www/html
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/99-app.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8080
ENTRYPOINT ["entrypoint"]

# ----------------------------------------------------------
# Stage: production (minimal, immutable, non-root)
# ----------------------------------------------------------
FROM base AS production

COPY --from=composer-deps /app ./
RUN chown -R www-data:www-data storage bootstrap/cache
USER www-data
HEALTHCHECK --interval=30s --timeout=3s CMD curl -fsS http://localhost:8080/up || exit 1