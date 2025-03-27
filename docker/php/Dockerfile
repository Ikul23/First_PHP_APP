FROM php:8.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libcurl4-openssl-dev \
    unzip \
    git \
    && docker-php-ext-install zip pdo pdo_mysql curl

# Копирование Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копирование composer-файлов
COPY composer.json composer.lock* ./

# Установка зависимостей
RUN composer install --no-interaction --no-scripts --no-autoloader

# Копирование всего проекта
COPY . .

# Генерация автозагрузчика
RUN composer dump-autoload --optimize