FROM php:8.2-apache

# PHP Extensions
RUN docker-php-ext-install pdo pdo_mysql

# Rewrite aktivieren
RUN a2enmod rewrite

# Document Root auf /public setzen
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

# Code kopieren
COPY . /var/www/html

# Rechte setzen
RUN chown -R www-data:www-data /var/www/html