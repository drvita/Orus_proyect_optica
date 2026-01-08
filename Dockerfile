# Stage 1: Base Image (Runtime + Extensions + Server)
FROM php:8.4-fpm-alpine AS base

# Install system dependencies, Nginx, and Supervisor
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    libpq \
    oniguruma-dev \
    libxml2-dev \
    nginx \
    supervisor \
    curl \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pgsql pdo_mysql mysqli bcmath gd zip intl pcntl opcache mbstring exif dom xml xmlwriter

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Create logs directory for supervisor
RUN mkdir -p /var/log/supervisor

WORKDIR /var/www/html

# Stage 2: PHP Dependencies
FROM base AS vendor

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copy necessary files for autoloader generation
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Stage 3: Final Image
FROM base

# 1. Layer configurations first (change rarely)
COPY ./docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# 3. Layer PHP dependencies (change occasionally)
COPY --from=vendor /var/www/html/vendor ./vendor

# 4. Layer application code last (change most frequently)
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Clear and optimize Laravel
RUN rm -f bootstrap/cache/*.php && \
    php artisan package:discover --ansi

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]