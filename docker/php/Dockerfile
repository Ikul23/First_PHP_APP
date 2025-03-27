FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip pdo pdo_mysql

# Копирование Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание директории перед установкой прав
RUN mkdir -p /var/www/html/src/Storage

RUN chmod -R 775 /var/www/html/src/Storage \
    && chown -R www-data:www-data /var/www/html/src/Storage

WORKDIR /var/www/html


RUN composer install --no-interaction --no-scripts --no-autoloader

# Копирование всего проекта
COPY . .

# Генерация автозагрузчика после копирования всего проекта
RUN composer dump-autoload --optimize