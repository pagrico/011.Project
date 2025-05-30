FROM php:8.0-apache

# Instala extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilita módulos de Apache si es necesario
RUN a2enmod rewrite
RUN a2enmod headers

# Configurar Apache para permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copia el código fuente al contenedor
COPY ./app /var/www/html
