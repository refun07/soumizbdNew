FROM php:8.2-apache

RUN apt-get update

RUN apt-get install -y \
    nodejs \
    npm \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libfreetype-dev \
	libjpeg62-turbo-dev \
    libwebp-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg && \ 
    docker-php-ext-install \
        bz2 \
        intl \
        iconv \
        bcmath \
        opcache \
        calendar \
        pdo_mysql \
        gd
        
RUN a2enmod rewrite headers

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chown -R www-data:www-data /var/www/html
