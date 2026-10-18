
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM php:8.4-apache

# Install PHP extensions and PostgreSQL support
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libonig-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install mbstring pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Remove all enabled Apache MPM module links
RUN find /etc/apache2/mods-enabled -maxdepth 1 \
        \( -name 'mpm_*.load' -o -name 'mpm_*.conf' \) \
        -delete \
    && a2enmod mpm_prefork headers rewrite

# Configure Apache to serve Laravel's public directory on port 8000
RUN sed -ri \
        's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf \
    && sed -ri \
        's/AllowOverride None/AllowOverride All/' \
        /etc/apache2/apache2.conf \
    && sed -ri \
        's/^Listen 80$/Listen 8000/' \
        /etc/apache2/ports.conf \
    && sed -ri \
        's/<VirtualHost \*:80>/<VirtualHost *:8000>/' \
        /etc/apache2/sites-available/000-default.conf \
    && apache2ctl -t

# PHP upload settings
RUN printf 'upload_max_filesize=3M\npost_max_size=80M\nmax_file_uploads=31\n' \
    > /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Copy application
COPY . .

# Copy compiled frontend assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Prepare Laravel writable directories
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV PORT=8000

EXPOSE 8000

CMD ["sh", "/var/www/html/docker-entrypoint.sh"]
