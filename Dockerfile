FROM php:7.4-apache

# -----------------------------
# Dependencias del sistema
# -----------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    curl \
    && docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        pgsql \
        pdo_pgsql

# -----------------------------
# Composer
# -----------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# -----------------------------
# Apache config
# -----------------------------
RUN a2enmod rewrite

RUN printf "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n" > /etc/apache2/conf-available/override.conf \
    && a2enconf override


RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# -----------------------------
# Copiar proyecto
# -----------------------------
COPY . /var/www/html/

# -----------------------------
# Instalar dependencias PHP
# -----------------------------
RUN cd /var/www/html && composer install --no-dev --optimize-autoloader --no-interaction

# Crear carpetas necesarias
RUN mkdir -p /var/www/html/application/logs \
    && mkdir -p /var/www/html/application/cache

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application

# Puerto
EXPOSE 80

CMD ["apache2-foreground"]
