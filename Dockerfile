FROM php:8.2-apache

SHELL ["/bin/bash", "-o", "pipefail", "-c"]
ENV DEBIAN_FRONTEND=noninteractive

# System deps
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      ca-certificates \
      curl \
      cron \
      python3 \
      python3-pip \
      zlib1g-dev \
      libonig-dev \
      libzip-dev \
      libwebp-dev \
      libjpeg62-turbo-dev \
      libpng-dev \
      libxml2-dev \
      libicu-dev \
      libcurl4-openssl-dev \
    ; \
    rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN set -eux; \
    docker-php-ext-configure gd --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" \
      gd \
      pdo_mysql \
      mysqli \
      mbstring \
      zip \
      bcmath \
      opcache \
      exif \
      pcntl \
      intl \
      xml \
      curl

# Apache modules
RUN set -eux; \
    a2enmod rewrite deflate headers expires include

# Composer (opzionale ma ok tenerlo)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer; \
    rm -f composer-setup.php; \
    composer --version

# App
WORKDIR /var/www/html
COPY . /var/www/html