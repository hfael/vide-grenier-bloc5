#!/bin/sh
set -eu

cd /var/www/html

mkdir -p logs public/storage
chmod -R 777 logs public/storage || true

if [ ! -f vendor/autoload.php ] || [ ! -d vendor/twig ]; then
    composer install --no-interaction --prefer-dist
fi

exec docker-php-entrypoint "$@"
