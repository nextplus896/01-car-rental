# Dockerfile
FROM php:8.4-fpm-alpine
WORKDIR /var/www/html

# System deps
RUN apk add --no-cache \
    bash tzdata git \
    icu-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    libzip-dev zip unzip

# PHP extensions (includes GD)
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
 && docker-php-ext-install -j"$(nproc)" gd intl bcmath mbstring pdo_mysql zip opcache

# Production opcache (optional file—see below)
COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Composer inside THIS image (so it sees the extensions)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_MEMORY_LIMIT=-1

# App code + install deps
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

# Permissions
RUN addgroup -g 1000 laravel && adduser -D -G laravel -u 1000 laravel \
 && chown -R laravel:laravel storage bootstrap/cache
USER laravel

EXPOSE 9000
