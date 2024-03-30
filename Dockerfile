FROM php:8.2-apache

RUN apt-get update && apt-get install -y libonig-dev libzip-dev libjpeg-dev libpng-dev libxml2-dev libicu-dev libcurl4-openssl-dev python3 python3-pip
RUN docker-php-ext-install pdo_mysql mysqli mbstring zip gd bcmath opcache exif pcntl intl xml curl
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;
RUN chmod +x /var/www/html/db

COPY ./* /var/www/html

RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2enmod headers
RUN a2enmod expires
RUN a2enmod include

WORKDIR /var/www/html