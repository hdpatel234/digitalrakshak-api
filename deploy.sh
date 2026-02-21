#!/bin/bash

echo "🚀 Deploy started"

cd /home/haresh/web/api.digitalrakshak.com/public_html || exit

git pull origin main

# install dependencies if needed
composer install --no-dev --optimize-autoloader

# run migrations if Laravel
php artisan migrate

# generate api docs
php artisan l5-swagger:generate --all

# clear cache
php artisan optimize:clear

echo "✅ Deploy finished"
