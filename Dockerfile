FROM php:8.1-fpm

RUN apt-get update -y \
    && apt-get install -y nginx \
    && apt-get install git -y

ENV PHP_CPPFLAGS="$PHP_CPPFLAGS -std=c++11"

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install opcache \
    && apt-get install libicu-dev -y \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && apt-get remove libicu-dev icu-devtools -y

# Composer installation
WORKDIR /home
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# PHP configuration
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/php-opocache-cfg.ini

# nginx configuration
COPY ./config/default.conf /etc/nginx/sites-enabled/default

# Entrypoint script
COPY ./config/entrypoint.sh /etc/entrypoint.sh
WORKDIR /etc

# RUN tr -d '\015' < entrypoint.sh > entrypoint.sh
RUN chmod +x entrypoint.sh

COPY --chown=www-data:www-data app/src /var/www/mysite

RUN mkdir -p /var/benz/migrations
RUN mkdir -p /var/benz/components/migrations

EXPOSE 80

WORKDIR /var/www/mysite
RUN composer install --no-dev --optimize-autoloader
ENTRYPOINT ["/bin/sh", "-c", "/etc/entrypoint.sh"]
