FROM php:8.5-cli-trixie

# Argumentos para recibir tu ID de usuario y grupo desde el host
ARG USER_ID=1000
ARG GROUP_ID=1000

# 1. Actualizamos e instalamos dependencias
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

# 2. Instalamos y configuramos Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# 3. Configuración de Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && touch /tmp/xdebug.log \
    && chmod 666 /tmp/xdebug.log

# 3. Creamos el usuario para que coincida con tu host
# Esto evita problemas de permisos al escribir archivos
RUN groupadd -g ${GROUP_ID} devuser || true && \
    useradd -u ${USER_ID} -g ${GROUP_ID} -m -s /bin/bash devuser

# 4. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cambiamos la propiedad de la carpeta de trabajo al nuevo usuario
RUN chown -R devuser:devuser /var/www/html

# Decimos a Docker que use este usuario de ahora en adelante
USER devuser

EXPOSE 8000