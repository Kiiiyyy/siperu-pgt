FROM php:8.3-apache

# 1. Install dependensi sistem & driver PostgreSQL (Supabase)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 2. Aktifkan mod_rewrite Apache untuk menangani Pretty URLs Laravel
RUN a2enmod rewrite

# 3. Ubah Document Root Apache agar langsung mengarah ke folder /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4. Copy seluruh source code project ke dalam container
COPY . /var/www/html

# 5. Install Composer & jalankan optimasi dependensi vendor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 6. Setel hak akses permissions folder storage agar bisa ditulis oleh sistem
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port web standar
EXPOSE 80

# 7. Jalankan optimasi cache dan jalankan migrasi otomatis saat container nyala
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    apache2-foreground