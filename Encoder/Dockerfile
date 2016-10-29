FROM phusion/baseimage:latest

ENV DEBIAN_FRONTEND noninteractive

RUN add-apt-repository universe && \
    apt-get update && \
    apt-get -yq install \
        ffmpeg \
        unzip \
        php7.0-cli php7.0-curl php7.0-zip php7.0-xml && \
    apt-get -yq upgrade && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    mkdir /app && \
    mkdir /etc/service/ffmpeg && \
    mkdir /etc/service/register

ADD . /app
RUN cd /app && \
    composer install

ADD run.ffmpeg.sh /etc/service/ffmpeg/run
ADD run.register.sh /etc/service/register/run
RUN chmod +x /etc/service/*/run
