#!/bin/bash

# php artisan down
rm -f bootstrap/cache/*.php
composer dump-autoload
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan debug:clear
php artisan view:clear
php artisan migrate
# php artisan db:seed
php artisan config:cache
php artisan route:cache
# php artisan up
