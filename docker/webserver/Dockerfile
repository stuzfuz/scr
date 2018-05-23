FROM php:7.2.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite
RUN service apache2 restart

RUN mkdir -p server/log/ && chmod 777 server/log/ -R
