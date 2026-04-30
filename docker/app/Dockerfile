FROM joeniland/laravel-ci:8.1-20240612 AS BUILD_PACKAGE

WORKDIR /buildapp

COPY composer.json composer.lock ./

ENV COMPOSER_NO_DEV=1

RUN composer --version && \
    composer install --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts && \
    composer dump-autoload --optimize

FROM local/phpfpm:latest

COPY --from=BUILD_PACKAGE /buildapp/vendor /var/www/html/vendor

# Xdebug

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

COPY docker/app/conf/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Clean pear cache
RUN rm -r /tmp/pear

WORKDIR /var/www/html