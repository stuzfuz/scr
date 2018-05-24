FROM php:7.2.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite
RUN service apache2 restart

RUN mkdir -p /var/www/html/server/log/ && chown www-data:www-data /var/www/html/server/log/ -R && chmod 777 /var/www/html/server/log/ -R
