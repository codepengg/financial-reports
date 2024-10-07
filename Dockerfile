# Menggunakan PHP dengan versi yang kompatibel dengan Laravel
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    libssl-dev \
    libsodium-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    exif \
    gd \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    sodium

RUN docker-php-ext-install pdo pdo_pgsql gd zip intl bcmath exif pcntl pdo_mysql sodium

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy aplikasi Laravel ke dalam container
COPY . .

# Ganti hak akses storage dan bootstrap cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Expose port 8000
EXPOSE 8000

# Copy entry point script
COPY run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh

# Set entrypoint script
ENTRYPOINT ["/usr/local/bin/run.sh"]
