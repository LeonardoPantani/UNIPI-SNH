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

# install pdo and pdo_mysql modules
RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo pdo_mysql

# install mysqli module
RUN docker-php-ext-install mysqli \
	&& docker-php-ext-enable mysqli

# copy composer.json
COPY ./src/libs/composer.json ${APACHE_APP_ROOT}/libs/

# install php libraries from composer.json
RUN composer update --no-cache --working-dir=libs

# setting ServerName so that Apache does not complain
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Redirect error pages to /storyforge/errors/x.php
RUN echo 'ErrorDocument 400 /storyforge/errors/400.php' >> /etc/apache2/apache2.conf && \
    echo 'ErrorDocument 401 /storyforge/errors/401.php' >> /etc/apache2/apache2.conf && \
    echo 'ErrorDocument 403 /storyforge/errors/403.php' >> /etc/apache2/apache2.conf && \
    echo 'ErrorDocument 404 /storyforge/errors/404.php' >> /etc/apache2/apache2.conf && \
	echo 'ErrorDocument 403 /storyforge/errors/405.php' >> /etc/apache2/apache2.conf && \
    echo 'ErrorDocument 500 /storyforge/errors/500.php' >> /etc/apache2/apache2.conf && \
	echo 'ErrorDocument 500 /storyforge/errors/501.php' >> /etc/apache2/apache2.conf

# change root from '/' to '/storyforge' in every apache configuration file
RUN sed -i "s|^\(.*\)\(DocumentRoot\) .*|\1\2 ${APACHE_DOCUMENT_ROOT}|g" /etc/apache2/sites-available/*.conf

# copy app files
COPY ./src/ ${APACHE_APP_ROOT}

RUN chown -R www-data:www-data .