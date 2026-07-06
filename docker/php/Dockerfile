FROM php:8.3-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
    && docker-php-ext-install \
        bcmath \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN groupmod -g ${GID} www-data \
    && usermod -u ${UID} -g ${GID} www-data \
    && mkdir -p /var/www/.composer \
    && chown -R www-data:www-data /var/www/.composer \
    && git config --system --add safe.directory /var/www/html

ENV COMPOSER_HOME=/var/www/.composer

USER www-data

CMD ["php-fpm"]
