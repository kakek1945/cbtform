FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package*.json vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci && npm run build

FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends git libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=frontend /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/form-cbt-entrypoint

RUN rm -f public/hot \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && chmod +x /usr/local/bin/form-cbt-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV LOG_CHANNEL=stderr
ENV PORT=8080

EXPOSE 8080

ENTRYPOINT ["form-cbt-entrypoint"]
CMD ["apache2-foreground"]
