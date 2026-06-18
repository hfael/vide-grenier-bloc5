FROM php:8.1-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y git unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod +x /usr/local/bin/app-entrypoint

WORKDIR /var/www/html

COPY . .

ENTRYPOINT ["app-entrypoint"]
CMD ["apache2-foreground"]
