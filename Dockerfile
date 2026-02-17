FROM php:7.4-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el proyecto
COPY . /var/www/html/

# Permisos
RUN chmod -R 755 /var/www/html/application/logs
RUN chmod -R 755 /var/www/html/application/cache
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto
EXPOSE 80

# Comando para ejecutar
CMD ["apache2-foreground"]
