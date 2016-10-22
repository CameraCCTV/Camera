<?php
// Routes

$app->get('/', \RatCam\Controllers\CameraController::class . ":renderHomepage");
$app->map(['get','post'], '/camera/ptz', \RatCam\Controllers\CameraController::class . ":doPtz");

$app->group("/api", function(){
    $this->group("/v1", function(){
        $this->get("/ping", \RatCam\Controllers\PingController::class . ':doPing');
    });
});