#!/usr/bin/env bash
set -e

if [ "$APP_ENV" = "development" ]; then
    echo "Running composer install..."
    composer install
fi

if [[ "$APP_ENV" = "production" ]]; then
    echo "Running composer install on production..."
    composer install --no-dev --optimize-autoloader
fi

echo "Starting FrankenPHP..."
exec frankenphp run --config /etc/caddy/Caddyfile
