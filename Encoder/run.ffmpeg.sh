#!/usr/bin/env bash

while true; do
    echo "Configured as $CAMERA_NAME to connect to $CAMERA_SOURCE";
    sleep 15;
    ffmpeg \
        -loglevel info \
        -threads 4 \
        $CAMERA_OPTIONS \
        -i $CAMERA_SOURCE \
        -framerate 25 \
        -vcodec libvpx \
        -c:a libopus \
        http://service:8080/$CAMERA_NAME.ffm;
done