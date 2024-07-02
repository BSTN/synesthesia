FROM php:8.2-fpm-alpine

# Workaround for DNS lookup error
RUN echo "nameserver 137.56.247.12" > /etc/resolv.conf

# Installing dependencies for the PHP modules
RUN apk update && apk add zip libzip-dev git

# Installing additional PHP modules
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Install Composer so it's available
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
