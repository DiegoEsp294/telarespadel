FROM php:7.4-apache

# -----------------------------
# Dependencias del sistema
# -----------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        pgsql \
        pdo_pgsql

# -----------------------------
# Apache config
# -----------------------------
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# -----------------------------
# Copiar proyecto
# -----------------------------
COPY . /var/www/html/

# Crear carpetas necesarias
RUN mkdir -p /var/www/html/application/logs \
    && mkdir -p /var/www/html/application/cache

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application

# Puerto
EXPOSE 80

CMD ["apache2-foreground"]
