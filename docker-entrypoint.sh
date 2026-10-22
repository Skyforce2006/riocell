#!/bin/sh
set -eu

port="${PORT:-10000}"
case "$port" in
    *[!0-9]* | '')
        echo "PORT must be a valid port number." >&2
        exit 1
        ;;
esac

sed -ri "s/^Listen [0-9]+/Listen ${port}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:([0-9]+)>/<VirtualHost *:${port}>/" \
    /etc/apache2/sites-available/000-default.conf

php artisan config:cache

exec "$@"
