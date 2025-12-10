# Must match PHP 8.2+
FROM php:8.2-apache  
composer update --with-all-dependencies

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Configure Apache for Render (port 10000)
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:10000/g' /etc/apache2/sites-available/*.conf

EXPOSE 10000
CMD ["apache2-foreground"]