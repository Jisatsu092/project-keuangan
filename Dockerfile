FROM php:8.2-apache

# Install extension yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy file Laravel ke dalam container
COPY . /var/www/html

# Set document root ke folder public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache config supaya pakai public/
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' \
    /etc/apache2/apache2.conf

# Enable .htaccess
RUN a2enmod rewrite

# Railway pakai PORT environment variable
EXPOSE ${PORT}

CMD ["apache2-foreground"]
