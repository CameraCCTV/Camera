#!/usr/bin/env bash

rm /app/conf/*.yml

while [ 1 ]
do
   su - www-data -c "ffserver";
done