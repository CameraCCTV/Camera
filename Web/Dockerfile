FROM thruio/docker-webapp

RUN apt-get update && \
    apt-get -yq install sudo && \
    curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash - && \
    apt-get -yq install nodejs ruby && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apt/archives/*.deb && \
    gem install sass

# Add worker service
RUN mkdir -p /etc/service/grunt
COPY docker/run.grunt.sh /etc/service/grunt/run
RUN chmod +x /etc/service/*/run

ADD . /app

RUN cd /app && \
    composer install && \
    npm install && \
    rm -f /var/www/html && ln -s /app/public /var/www/html && \
    chmod -R 777 /app/logs/ && \
    rm -Rf /app/.sass-cache/*



