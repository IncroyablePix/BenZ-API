#!/bin/sh
set -e
php /var/www/mysite/vendor/bin/doctrine-migrations diff
php /var/www/mysite/vendor/bin/doctrine-migrations migrate --no-interaction --write-sql
php-fpm -D
nginx -g 'daemon off;'
