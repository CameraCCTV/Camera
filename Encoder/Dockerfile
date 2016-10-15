FROM phusion/baseimage:latest

ENV DEBIAN_FRONTEND noninteractive

RUN add-apt-repository universe && \
    apt-get update && \
    apt-get -yq install \
        ffmpeg \
        php7.0-cli php7.0-curl && \
    apt-get -yq upgrade && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN mkdir /app
ADD . /app
RUN mkdir /etc/service/ffmpeg
RUN mkdir /etc/service/register
ADD run.ffmpeg.sh /etc/service/ffmpeg/run
ADD run.register.sh /etc/service/register/run
RUN chmod +x /etc/service/*/run
