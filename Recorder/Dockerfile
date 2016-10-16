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


# Add worker service
RUN mkdir                   /etc/service/get-config
#RUN mkdir                   /etc/service/record-front
ADD run.get-config.sh       /etc/service/get-config/run
#ADD run.record-front.sh    /etc/service/record-front/run
RUN chmod +x                /etc/service/*/run

RUN mkdir /video

ADD . /app




