FROM php:8.3-apache

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
	ssl-cert \
	less \
	wget \
	vim \
	libapache2-mod-evasive

RUN pecl install redis \
	&& docker-php-ext-enable redis

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

# apache2 RewriteEngine
RUN a2enmod rewrite

# apache2 mod_ssl
RUN a2enmod ssl

# apache2 evasive config
RUN sed -i /etc/apache2/mods-available/evasive.conf \
		-e '2,7 s/^\(\s*\)#/\1/' \
		-e 's/^\(\s*DOSSiteCount\s*\)[0-9]\+/\110/' \
		-e 's/^\(\s*DOSBlockingPeriod\s*\)[0-9]\+/\125/'

# apache2 evasive activation
ARG EVASIVE_MOD=
RUN [ -n "${EVASIVE_MOD}" ] && a2enmod evasive || a2dismod evasive

# apache2 HTTPS env
RUN a2ensite default-ssl.conf

# setting ServerName so that Apache does not complain
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# change root from '/var/www/html' to '/var/www/html/public' in every apache configuration file
ARG APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i "s|^\(.*\)\(DocumentRoot\) .*|\1\2 ${APACHE_DOCUMENT_ROOT}|g" /etc/apache2/sites-available/*.conf

# copy app files
COPY ./src/ ${APACHE_APP_ROOT}

# set owner permissions
RUN chown -R www-data:www-data .