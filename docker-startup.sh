#!/bin/sh

sleep 10s
php artisan key:generate --force --no-interaction
php artisan migrate --seed --force --no-interaction
php artisan storage:link --force --no-interaction
php artisan serve --host=0.0.0.0 --port=8002

