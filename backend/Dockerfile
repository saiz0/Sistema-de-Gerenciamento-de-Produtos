FROM composer:2 AS composer-base

WORKDIR /app

COPY composer.json composer.lock ./


FROM composer-base AS dependencies-development

RUN composer install \
    --no-interaction \
    --no-scripts \
    --prefer-dist


FROM composer-base AS dependencies-production

RUN composer install \
    --classmap-authoritative \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist


FROM php:8.4-cli-alpine AS php-runtime

RUN apk add --no-cache \
        icu-libs \
        libpq \
        libzip \
        oniguruma \
        sqlite-libs \
        su-exec \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        sqlite-dev \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        opcache \
        pdo_pgsql \
        pdo_sqlite \
        zip \
    && apk del .build-deps

WORKDIR /var/www/html

COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/docker-entrypoint

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]


FROM php-runtime AS development

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=dependencies-development --chown=www-data:www-data /app/vendor ./vendor
COPY --chown=www-data:www-data . .

RUN php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache


FROM php-runtime AS production

COPY --from=dependencies-production --chown=www-data:www-data /app/vendor ./vendor
COPY --chown=www-data:www-data . .

RUN php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache
