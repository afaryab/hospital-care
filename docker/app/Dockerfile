# ── Stage 1: Frontend assets ─────────────────────────────────────────────────
# PHP base so Wayfinder can call `php artisan` during the Vite build.
FROM php:8.4-cli-alpine AS frontend
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Install zip extension (required by config/backup.php at boot) + Node/pnpm
RUN apk add --no-cache nodejs npm libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-install zip \
    && apk del .build-deps \
    && npm install -g pnpm
ENV CI=true
WORKDIR /build
# Composer deps (vendor needed so artisan can bootstrap)
COPY composer.json composer.lock ./
COPY packages/ ./packages/
RUN composer install --no-dev --no-scripts --ignore-platform-reqs --no-interaction
# JS deps
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
# Application source
COPY . .
# Generate APP_KEY without bootstrapping Laravel (avoids loading all service providers)
RUN cp .env.example .env \
    && KEY=$(php -r "echo base64_encode(random_bytes(32));") \
    && sed -i "s|^APP_KEY=.*$|APP_KEY=base64:${KEY}|" .env \
    && mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
RUN pnpm run build

# ── Stage 2: Runtime image ────────────────────────────────────────────────────
FROM php:8.4-fpm-alpine

ARG SENTRY_RELEASE
ARG SENTRY_ENVIRONMENT=production
ARG APP_ENV=production
ENV SENTRY_RELEASE=${SENTRY_RELEASE} \
    SENTRY_ENVIRONMENT=${SENTRY_ENVIRONMENT} \
    APP_ENV=${APP_ENV}

# Install runtime packages; compile PHP extensions inside a virtual build-dep
# group that gets removed afterwards — keeps build tools out of the final image.
RUN apk add --no-cache \
        bash curl git unzip zip \
        libpng libxml2 oniguruma icu-libs freetype libjpeg-turbo nodejs npm libzip \
        supervisor nginx apache2-utils dcron \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev libxml2-dev oniguruma-dev icu-dev freetype-dev \
        libjpeg-turbo-dev libzip-dev autoconf gcc g++ make \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear \
    && npm install -g pnpm

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/app/php.ini /usr/local/etc/php/conf.d/custom-php.ini

# Configure PHP-FPM socket in one sed pass
RUN sed -i \
    -e 's/listen = 127.0.0.1:9000/listen = 9000/' \
    -e 's/;listen.owner = www-data/listen.owner = www-data/' \
    -e 's/;listen.group = www-data/listen.group = www-data/' \
    -e 's/;listen.mode = 0660/listen.mode = 0660/' \
    /usr/local/etc/php-fpm.d/www.conf

# Nginx user and log directories
RUN adduser -D -s /bin/sh nginx nginx 2>/dev/null || true \
    && mkdir -p /var/log/nginx /var/lib/nginx/tmp /var/cache/nginx /var/log/supervisor \
    && chown -R nginx:nginx /var/log/nginx /var/lib/nginx /var/cache/nginx \
    && touch /etc/nginx/.htpasswd

COPY docker/app/nginx.conf /etc/nginx/nginx.conf
COPY docker/app/default.conf /etc/nginx/conf.d/default.conf
COPY docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/app/start.sh /start.sh
RUN chmod +x /start.sh

WORKDIR /var/www/html

# Copy application source then overlay production-built frontend assets
COPY . .
COPY --from=frontend /build/public/build ./public/build

# Install PHP dependencies, generate app key, set up storage directories
RUN mkdir -p bootstrap/cache storage/app \
            storage/framework/cache storage/framework/sessions \
            storage/framework/views storage/logs \
    && composer install --no-dev --optimize-autoloader \
    && cp .env.example .env \
    && php artisan key:generate --force \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80 443
CMD ["/bin/sh", "/start.sh"]
