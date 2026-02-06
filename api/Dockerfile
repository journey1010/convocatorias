FROM php:8.5-cli-trixie

# 1. Actualizamos e instalamos dependencias del sistema usando APT
# Debian requiere libpq-dev para Postgres y libzip-dev para Zip.
# mbstring en Debian requiere que instales oniguruma si no viene pre-compilado.
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
    # Limpieza de cache para que la imagen no pese tanto
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]