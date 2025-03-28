# backend/Dockerfile
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo_mysql zip

# Set working directory
WORKDIR /var/www/html

# Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Copy entire project
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist

## ✅ Fix permissions for var/ (cache/logs)
#RUN chown -R www-data:www-data var \
#    && chmod -R 775 var

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
