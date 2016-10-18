#!/usr/bin/env bash

while true; do
    echo "Configured as $CAMERA_NAME to connect to $CAMERA_SOURCE";
    sleep 30;
    openRTSP -t $CAMERA_SOURCE | \
    ffmpeg \
        -i - \
        -f webm \
        -an http://service:8080/$CAMERA_NAME.ffm;
done