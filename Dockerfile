FROM php:8.2-fpm-alpine as syn-php-fpm

# Workaround for DNS lookup error
RUN echo "nameserver 137.56.247.12" > /etc/resolv.conf

# Installing dependencies for the PHP modules
RUN apk update && apk add zip libzip-dev git

# Installing additional PHP modules
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Install Composer so it's available
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY server /var/www/html

RUN composer install
RUN git clone --depth=1 https://github.com/BSTN/synesthesia_config -b kinderen && rm -rf synesthesia_config/.git

FROM node:14-alpine as yarn

COPY package.json /app/package.json

WORKDIR /app

RUN yarn install

COPY webpack.config.js /app/webpack.config.js
COPY app /app/app
COPY server /app/server

RUN yarn webpack

FROM nginx:latest as syn-nginx

COPY nginx/nginx.conf /etc/nginx/conf.d/default.conf

COPY server /var/www/html

COPY --from=yarn /app/server/js /var/www/html/js

