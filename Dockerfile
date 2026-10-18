
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM php:8.4-cli

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

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build /var/www/html/public/build

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV PORT=8080

EXPOSE 8080

CMD ["sh", "-c", "php artisan optimize:clear && php artisan migrate --force && exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
