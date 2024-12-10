FROM php:7.2-fpm

# Installeer systeempakketten
RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libxml2-dev \
    libgmp-dev \
    libcurl4-openssl-dev \
    unzip

# Configureer en installeer GD-extensie
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd

# Installeer overige PHP-extensies
RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring xml xmlrpc gmp curl

# Installeer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer