FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    postgresql-client \
    unzip \
    zip \
    linux-headers \
    $PHPIZE_DEPS \
 && docker-php-ext-install intl pdo pdo_pgsql \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
