FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    curl \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip calendar

# Install Composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN a2enmod rewrite
COPY ./.htaccess /var/www/html/.htaccess