#!/bin/bash
cd Encoder;
composer install;
cd -;

cd Recorder;
composer install;
cd -;

cd Service;
chmod 777 conf;
composer install;
cd -;

cd Web;
composer install;
cd -;

docker-compose build;