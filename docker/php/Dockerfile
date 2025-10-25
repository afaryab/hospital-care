FROM php:7.4-apache

# Install required system packages and PHP extensions
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ || true
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli zip gd mbstring xml

# Enable apache mod_rewrite
RUN a2enmod rewrite

# Copy custom php.ini and vhost (paths are relative to the compose build context)
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
