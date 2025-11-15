FROM php:8.2-apache

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install extension Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy project ke container
COPY . /var/www/html

WORKDIR /var/www/html

# Install vendor
RUN composer install --no-dev --optimize-autoloader

# Set document root ke public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' \
    /etc/apache2/apache2.conf

RUN a2enmod rewrite

EXPOSE ${PORT}

CMD ["apache2-foreground"]
