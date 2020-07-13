FROM php:7-apache

ENV TZ="Europe/London"
WORKDIR /var/www/html/

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    git

# Copy the test php files
COPY index.php /var/www/html
COPY composer.json /var/www/html

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer \
    && php -r "unlink('composer-setup.php');";

# Use composer to install the dependancies defined in composer.json
RUN composer install
