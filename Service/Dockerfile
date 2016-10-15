FROM phusion/baseimage:latest

ENV DEBIAN_FRONTEND noninteractive

RUN add-apt-repository universe && \
    apt-get update && \
    apt-get -yq install \
        wget \
        ffmpeg \
        nano \
        multitail \
        aptitude \
        unzip \
        git \
        curl \
        apache2 \
        libapache2-mod-php7.0 \
        php7.0 \
        php-all-dev \
        php7.0-mysql \
        php7.0-curl \
        php-apcu \
        php7.0-gd \
        php7.0-intl \
        php7.0-cli \
        php7.0-mcrypt \
        php7.0-soap \
        php7.0-sqlite \
        php7.0-opcache \
        php7.0-ldap \
        php7.0-mbstring \
        php7.0-json \
        php7.0-xml \
        ca-certificates && \
    apt-get -yq upgrade && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Build xDebug for PHP7
RUN cd /tmp && \
    wget -O xdebug.tgz http://xdebug.org/files/xdebug-2.4.1.tgz && \
    tar -xvzf xdebug.tgz && \
    cd xdebug-2.4.1 && \
    phpize && \
    ./configure && \
    make && \
    cp modules/xdebug.so /usr/lib/php/20151012

RUN ls -lah /etc/php/7.0

RUN sed -i "s/upload_max_filesize.*/upload_max_filesize = 1024M/g" /etc/php/7.0/apache2/php.ini && \
    sed -i "s/post_max_size.*/post_max_size = 1024M/g" /etc/php/7.0/apache2/php.ini && \
    sed -i "s/max_execution_time.*/max_execution_time = 0/g" /etc/php/7.0/apache2/php.ini && \
    sed -i "s/variables_order.*/variables_order = \"EGPCS\"/g" /etc/php/7.0/apache2/php.ini && \
    sed -i "s/error_reporting.*/error_reporting = E_ALL \& \~E_DEPRECATED \& \~E_STRICT \& \~E_CORE_WARNING/g" /etc/php/7.0/apache2/php.ini && \
    cp /etc/php/7.0/apache2/php.ini /etc/php/7.0/cli/php.ini && \
    echo "\n\nzend_extension = /usr/lib/php/20151012/xdebug.so\n" >> /etc/php/7.0/cli/php.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Set container Time Zone
RUN echo Europe/London > /etc/timezone && dpkg-reconfigure --frontend noninteractive tzdata

# Configure /app folder with sample app
RUN mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html
WORKDIR /app

# Set up Apache
ADD processes/ApacheConfig.conf /etc/apache2/sites-enabled/000-default.conf
ADD processes/apache2.conf /etc/apache2/apache2.conf
#ADD processes/envvars /etc/apache2/envvars
RUN a2enmod rewrite

# Add ffserver service
RUN mkdir /etc/service/ffserver
ADD processes/run.ffserver.sh /etc/service/ffserver/run

# Add Apache Startup Script
RUN mkdir /etc/service/apache2
ADD processes/run.apache.sh /etc/service/apache2/run

RUN chmod +x /etc/service/*/run

ADD processes/ffserver.conf /etc/ffserver.conf
RUN chmod 777 /etc/ffserver.conf

RUN chsh -s /bin/bash www-data

ADD ./ /app
EXPOSE 80 8080
