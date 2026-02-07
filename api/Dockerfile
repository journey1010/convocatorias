FROM php:8.5-cli-trixie

# 1. Actualizamos e instalamos dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalamos Xdebug vía PECL
# Usamos pecl para descargar el binario y docker-php-ext-enable para activarlo
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# 3. Configuración de Xdebug
# client_host=host.docker.internal permite que el contenedor se comunique con tu PC
# En la sección de Xdebug, agrega esta línea:
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=false" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# 4. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000
