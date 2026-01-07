# Usar PHP FPM Alpine como base para una imagen más ligera
FROM php:8.4-fpm-alpine AS builder

# Instalar dependencias necesarias
RUN apk add --no-cache \
    linux-headers \
    $PHPIZE_DEPS \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev

# Instalar y configurar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache \
    intl

# Instalar Redis vía PECL
RUN pecl install redis && \
    docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuración de PHP para producción
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini && \
    sed -i 's/memory_limit = 128M/memory_limit = 256M/g' $PHP_INI_DIR/php.ini && \
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 64M/g' $PHP_INI_DIR/php.ini && \
    sed -i 's/post_max_size = 8M/post_max_size = 64M/g' $PHP_INI_DIR/php.ini

# Configurar OPcache para mejor rendimiento
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Crear usuario no root
ARG UID=1000
ARG GID=1000
RUN addgroup -g $GID laravel && \
    adduser -u $UID -G laravel -h /var/www -s /bin/sh -D laravel

# Configurar permisos
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R laravel:laravel /var/www

# Directorio de trabajo
WORKDIR /var/www

# Cambiar a usuario no privilegiado
USER laravel

# Exponer puerto FPM
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
