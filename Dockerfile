# --- build vendor in an isolated layer ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress
COPY . .
# do NOT run artisan here (no env yet)

# --- runtime: PHP-FPM 8.2 with needed extensions ---
FROM php:8.2-fpm-alpine AS app
WORKDIR /var/www/html

# system deps
RUN apk add --no-cache bash git icu-dev oniguruma-dev libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev zip unzip

# php extensions (common for Laravel)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) bcmath intl gd pdo pdo_mysql opcache

# opcaches for prod
COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# app code
COPY --from=vendor /app /var/www/html

# permissions for storage & cache
RUN addgroup -g 1000 laravel && adduser -D -G laravel -u 1000 laravel \
 && chown -R laravel:laravel storage bootstrap/cache
USER laravel

# php-fpm listens on 9000 by default
