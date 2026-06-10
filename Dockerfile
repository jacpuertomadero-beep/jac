FROM php:8.2-apache

# Instalar extensión PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar aplicación
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80