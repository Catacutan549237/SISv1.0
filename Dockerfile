# Use PHP with built-in web server
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first (optimize caching)
COPY composer.json composer.lock ./

# Install dependencies (FIX: Add --no-scripts flag if having issues)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Apache configuration for Render
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 10000 (Render's default for web services)
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]