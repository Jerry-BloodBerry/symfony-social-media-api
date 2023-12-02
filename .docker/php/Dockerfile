FROM php:8.2-fpm-bullseye AS base

ARG TIMEZONE
ARG XDEBUG_CLIENT_HOST
ARG XDEBUG_AUTO_DISCOVER_HOST

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN apt-get update && apt-get install -y \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    libpq-dev \
    libxml2-dev \
    librabbitmq-dev \
    acl \
    cron \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc

RUN docker-php-ext-configure gd --with-jpeg --with-freetype

RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql zip xsl gd intl opcache exif mbstring sockets

RUN pecl install amqp-1.11.0

RUN docker-php-ext-enable amqp

# Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=${XDEBUG_AUTO_DISCOVER_HOST}}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

FROM composer:lts AS build

ARG APP_ENV

WORKDIR /app

COPY composer.json .
COPY composer.lock .

RUN \
    if [ "$APP_ENV" = 'dev' ]; then composer install --no-scripts --ignore-platform-reqs; \
    else composer install --no-dev --no-scripts --ignore-platform-reqs; \
    fi

COPY . .
RUN composer dumpautoload --optimize

FROM base AS final

COPY --from=build /app /var/www/app
COPY --from=build /usr/bin/composer /usr/bin/composer

COPY .docker/php/startup.sh /home/root/
RUN chmod +x /home/root/startup.sh

RUN usermod -u 1000 www-data
RUN chown -R www-data:1000 /var/www/app
WORKDIR /var/www/app

CMD ["/bin/bash", "/home/root/startup.sh"]
EXPOSE 9000