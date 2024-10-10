FROM docker.io/php:8.2-fpm AS syn-php-fpm

# Install dependencies for the PHP modules
RUN set -ex; \
    export UCF_FORCE_CONFFOLD=1 DEBIAN_FRONTEND=noninteractive; \
    apt-get update; \
    apt-get -o Dpkg::Options::=--force-confold -Vqy dist-upgrade; \
    apt-get -o Dpkg::Options::=--force-confold -Vqy --no-install-recommends install libzip-dev; \
    find /var/*/apt -type f -delete

# Install additional PHP modules
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Install Composer so it's available
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY server /var/www/html

RUN composer install

RUN curl -sSL https://github.com/BSTN/synesthesia_config/archive/kinderen.tar.gz | tar xz --transform 's,^[^/]*,synesthesia_config,'


FROM docker.io/node:14 AS yarn

COPY package.json /app/package.json

WORKDIR /app

RUN yarn install

COPY webpack.config.js /app/webpack.config.js
COPY app /app/app
COPY server /app/server

RUN yarn webpack


FROM docker.io/nginx:latest AS syn-nginx

COPY nginx/nginx.conf /etc/nginx/conf.d/default.conf

COPY server /var/www/html

COPY --from=yarn /app/server/js /var/www/html/js
