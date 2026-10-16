FROM php:8.2-apache

# Install PDO MySQL and required extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Expose port (Railway dynamically injects PORT)
EXPOSE 80

# Script to use Railway dynamic port if provided
CMD sh -c "sed -i 's/80/'\${PORT:-80}'/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf && apache2-foreground"
