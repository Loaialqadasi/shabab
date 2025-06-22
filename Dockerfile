FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable mod_rewrite if you use .htaccess
RUN a2enmod rewrite

# Copy all files to the Apache server root
COPY . /var/www/html/

# Set permissions for uploads
RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads

# Expose port 80
EXPOSE 80
