FROM php:8.2-apache

RUN apt-get update && apt-get install -y \ 
    git unzip libpq-dev libzip-dev zip \ 
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

RUN a2enmod rewrite

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf\ 
 && sed -i 's|var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

COPY . /var/www/html

RUN mkdir -p /var/www/html/public/uploads\ 
&& chown -R www-data:www-data /var/www/html/public/uploads \
&& chmod -R 755 /var/www/html/public/uploads

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 

EXPOSE 10000

CMD ["apache2-foreground"]
