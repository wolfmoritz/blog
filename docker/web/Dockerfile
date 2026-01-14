# Base image: PHP 8.4 with Apache
FROM php:8.4-apache

# Install system utilities
RUN apt-get update && apt-get install -y \
    nano \
    curl \
    zip \
    unzip \
    libmagickwand-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    build-essential \
    git \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    mbstring

# Install Imagick extension
RUN pecl install imagick && docker-php-ext-enable imagick

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
