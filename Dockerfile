FROM php:8.3-apache

ARG APACHE_DOCUMENT_ROOT=/var/www/html
ARG APACHE_APP_ROOT=/var/www/html

# probably useless
WORKDIR ${APACHE_APP_ROOT}

# install composer
COPY --from=composer:2.8.1 /usr/bin/composer /usr/bin/composer

# install tools
RUN apt-get update && apt-get install -y \
	git \
	zip \
	unzip \
	ssl-cert

# apache2 mod_ssl
RUN a2enmod ssl

# apache2 HTTPS env
RUN a2ensite default-ssl.conf

# install mysqli module
RUN docker-php-ext-install mysqli \
	&& docker-php-ext-enable mysqli

# copy app files
COPY ./app/ ${APACHE_APP_ROOT}

# install php libraries from composer.json
RUN composer update --no-cache --working-dir=libs

# setting ServerName so that Apache does not complain
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN sed -i "s|^\(.*\)\(DocumentRoot\) .*|\1\2 ${APACHE_DOCUMENT_ROOT}|g" /etc/apache2/sites-available/*.conf