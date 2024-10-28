# Use the official PHP image as the base
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www/alimal-digital-system

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . /var/www/alimal-digital-system

# Give permissions to storage and cache directories
RUN chown -R www-data:www-data /var/www/alimal-digital-system \
    && chmod -R 775 /var/www/alimal-digital-system/storage /var/www/alimal-digital-system/bootstrap/cache

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]