FROM php:8.1.13-fpm

RUN usermod -u 1000 www-data
RUN usermod -G staff www-data

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libpq-dev libzip-dev zip unzip \
    sudo wget sqlite3 libsqlite3-dev libzstd-dev liblz4-dev && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-configure mysqli --with-mysqli=mysqlnd

RUN yes | pecl install msgpack igbinary redis

RUN docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pgsql && \
    docker-php-ext-install pdo_pgsql && \
    docker-php-ext-install pdo_sqlite && \
    docker-php-ext-install gd && \
    docker-php-ext-install opcache && \
    docker-php-ext-install zip && \
    docker-php-ext-install calendar && \
    docker-php-ext-install sockets && \
    docker-php-ext-install pcntl && \
    docker-php-ext-enable igbinary && \
    docker-php-ext-enable msgpack && \
    docker-php-ext-enable redis 

RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.load_comments=1'; \
} >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

RUN { \
    echo 'upload_max_filesize=100M'; \
    echo 'post_max_size=100M'; \
    echo 'log_errors=1'; \
    echo 'display_errors=0'; \
    echo 'error_reporting=E_ERROR'; \
} >> /usr/local/etc/php/conf.d/docker-php-core.ini

RUN rm -rf /var/www/html \
    && chmod 0777 /tmp/
