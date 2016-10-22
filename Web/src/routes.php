<?php
// Routes

$app->get('/', \MattCam\Controllers\CameraController::class . ":renderHomepage");
$app->map(['get','post'], '/camera/ptz', \MattCam\Controllers\CameraController::class . ":doPtz");

$app->group("/api", function(){
    $this->group("/v1", function(){
        $this->get("/ping", \MattCam\Controllers\PingController::class . ':doPing');
    });
});