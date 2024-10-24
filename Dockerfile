FROM php:8.3-apache

# probably useless
WORKDIR /var/www/html/

# install composer
COPY --from=composer:2.8.1 /usr/bin/composer /usr/bin/composer

# install tools
RUN apt-get update && apt-get install -y \
	git \
	zip \
	unzip

# install mysqli module
RUN docker-php-ext-install mysqli \
	&& docker-php-ext-enable mysqli

# install pho libraries
RUN composer require \
	phpmailer/phpmailer \
	monolog/monolog
