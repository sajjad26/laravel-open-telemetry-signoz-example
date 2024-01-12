# Use PHP 8.1 FPM as the base image
FROM php:8.1-fpm

# Set working directory in the container
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/sites-available/default

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Expose port 80 for Nginx
EXPOSE 80

# Start-up script to start Nginx and PHP-FPM
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
