FROM php:8.2-apache

# تفعيل mod_rewrite
RUN a2enmod rewrite

# السماح لـ .htaccess بالعمل
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN docker-php-ext-install mysqli pdo pdo_mysql

EXPOSE 80