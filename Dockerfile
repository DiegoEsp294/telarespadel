FROM php:7.4-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el proyecto
COPY . /var/www/html/

# Crear carpetas necesarias (si no existen)
RUN mkdir -p /var/www/html/application/logs \
    && mkdir -p /var/www/html/application/cache

# Permisos correctos para Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application

RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Exponer puerto
EXPOSE 80

# Ejecutar Apache
CMD ["apache2-foreground"]
