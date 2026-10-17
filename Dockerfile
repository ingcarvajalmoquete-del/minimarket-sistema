FROM php:8.2-cli

# Install PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

# Copy application files
COPY . /app

# Expose port (Railway passes PORT environment variable)
EXPOSE 8080

# Run PHP built-in server with public/ as document root
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
