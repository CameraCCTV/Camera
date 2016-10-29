#!/usr/bin/env bash

while true; do
    echo "Configured as $CAMERA_NAME to connect to $CAMERA_SOURCE";
    sleep 15;
    openRTSP \
        -v \
        -D 10 \
        $CAMERA_SOURCE | \
    ffmpeg \
        -loglevel info \
        -threads 4 \
        -i - \
        -framerate 25 \
        http://service:8080/$CAMERA_NAME.ffm;
done