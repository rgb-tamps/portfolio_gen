
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

# Enable only Apache's prefork MPM, which is required by mod_php.
RUN find /etc/apache2/mods-enabled -maxdepth 1 \
        \( -name 'mpm_*.load' -o -name 'mpm_*.conf' \) \
        -delete \
    && ln -s ../mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && if [ -f /etc/apache2/mods-available/mpm_prefork.conf ]; then \
        ln -s ../mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf; \
    fi \
    && a2enmod headers rewrite \
    && apache_modules="$(apache2ctl -M 2>&1)" \
    && mpm_count="$(printf '%s\n' "$apache_modules" | grep -Ec '^[[:space:]]*mpm_[[:alnum:]_]+_module[[:space:]]')" \
    && [ "$mpm_count" -eq 1 ] \
    && printf '%s\n' "$apache_modules" | grep -q 'mpm_prefork_module'

# Configure Apache to serve Laravel's public directory. The entrypoint sets
# the listener and virtual host to Railway's assigned PORT at container start.
RUN sed -ri \
        's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf \
    && sed -ri \
        's/AllowOverride None/AllowOverride All/' \
        /etc/apache2/apache2.conf \
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
