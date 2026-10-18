
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM php:8.4-apache

# Install PHP dependencies and PostgreSQL support
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libonig-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        mbstring \
        pdo_pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache: use only mpm_prefork
# Fix Apache MPM conflict
RUN for module in mpm_event mpm_worker mpm_prefork; do \
        a2dismod "$module" 2>/dev/null || true; \
    done \
    && rm -f /etc/apache2/mods-enabled/mpm_*.load \
             /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork \
    && a2enmod headers rewrite \
    && apache2ctl -t

# Point Apache to Laravel's public directory
RUN sed -ri \
    's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf \
    && sed -ri \
    's/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

# PHP upload settings
RUN printf 'upload_max_filesize=3M\npost_max_size=80M\nmax_file_uploads=31\n' \
    > /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Copy Laravel application
COPY . .

# Copy compiled frontend assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Ensure Laravel can write cache and log files
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Railway uses the configured service port
ENV PORT=8080

EXPOSE 8080

CMD ["sh", "/var/www/html/docker-entrypoint.sh"]
