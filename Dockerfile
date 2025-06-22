FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pgsql pdo_pgsql
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
