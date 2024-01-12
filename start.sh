#!/bin/bash

# Start Nginx
echo "Starting nginx"
service nginx start

# Start PHP-FPM
echo "Starting PHP FPM"
php-fpm
