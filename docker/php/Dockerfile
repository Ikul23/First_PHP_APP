FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/src/Storage/twig_cache && \
    chown -R www-data:www-data /var/www/html/src/Storage/twig_cache

WORKDIR /var/www/html