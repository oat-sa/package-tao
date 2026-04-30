FROM webdevops/php:8.1

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y --no-install-recommends nodejs \
 && rm -rf /var/lib/apt/lists/* \
 && find /usr/local/etc/php -name '*ioncube*' -delete

WORKDIR /app
