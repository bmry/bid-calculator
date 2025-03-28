# backend/Dockerfile
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install intl pdo_mysql zip

WORKDIR /var/www/html

# Copy composer files separately for cache
COPY composer.json ./

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

COPY composer.json composer.lock ./

# Install PHP dependencies (production or dev)
RUN composer install --no-interaction --prefer-dist

# Copy project files (if you don't want to rely on volume)
# For local development with volumes, you might skip this
# COPY . /var/www/html

# Expose 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
