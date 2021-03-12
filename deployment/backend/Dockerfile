FROM php:7.4-apache

# install utilities
RUN apt-get update \
    && apt-get install -y --no-install-recommends curl zip unzip locales apt-utils vim git libicu-dev software-properties-common;

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
    && echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    && locale-gen

# install composer
RUN curl -sSk https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install php ext
RUN docker-php-ext-install pdo_mysql intl

# start xdebug
RUN pecl install redis-5.3.3 \
    && pecl install xdebug-3.0.3 \
    && docker-php-ext-enable redis xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN a2enmod rewrite

# config apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo 'alias ll="ls -la"' >> ~/.bashrc

RUN composer install \
    && service apache2 start

# todo start apache2

WORKDIR /var/www/html
