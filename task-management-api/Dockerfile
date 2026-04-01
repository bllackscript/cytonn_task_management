# Stage 1: Build PHP dependencies and assets
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    postgresql-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --no-dev --optimize

# -- Stage 2: Production Image --
FROM php:8.2-fpm-alpine

# Install system dependencies for runtime
RUN apk add --no-cache \
    nginx \
    postgresql-libs \
    libpng \
    libxml2 \
    zip \
    unzip \
    curl

# Install PHP extensions for runtime
RUN docker-php-ext-install pdo pdo_pgsql pgsql bcmath gd

# Set up Nginx configuration
RUN mkdir -p /run/nginx
COPY nginx/default.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www/html

# Copy application from builder stage
COPY --from=builder --chown=www-data:www-data /app /var/www/html

# Create deployment script directory
RUN mkdir -p /var/www/html/scripts
COPY scripts/render-deploy.sh /usr/local/bin/render-deploy.sh
RUN chmod +x /usr/local/bin/render-deploy.sh

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (Render uses 10000 by default, but we can use PORT env var)
EXPOSE 10000

# Start Render Deploy Script, Nginx, and PHP-FPM
CMD ["sh", "-c", "render-deploy.sh && php-fpm -D && nginx -g 'daemon off;'"]
