FROM php:7.4-fpm-alpine as base

WORKDIR /var/www/html

RUN apk --update add \
    build-base \
    autoconf \
    libzip-dev \
    postgresql-dev \
    zip

RUN docker-php-ext-install sockets zip pdo pdo_pgsql

RUN echo "memory_limit=512M" >> /usr/local/etc/php/php.ini

USER www-data
ENV APP_ENV=prod

COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT -1

COPY composer.json .
COPY composer.lock .

# ---- Dependencies ----
FROM base AS dependencies
# install vendors
USER www-data
RUN APP_ENV=prod composer install --prefer-dist --no-plugins --no-scripts --no-dev --optimize-autoloader
RUN mkdir bin

# ---- Release ----
FROM base AS release

USER www-data
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=dependencies /var/www/html/bin bin
COPY --chown=www-data:www-data --from=dependencies /var/www/html/vendor /var/www/html/vendor
RUN touch .env