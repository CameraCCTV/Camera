#!/bin/bash

source /etc/apache2/envvars
sleep 3;
exec /usr/sbin/apache2 -D FOREGROUND
