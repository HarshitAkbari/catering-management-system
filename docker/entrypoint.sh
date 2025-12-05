#!/bin/bash

# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Run migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm
