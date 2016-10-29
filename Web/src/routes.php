<?php
// Routes

$app->get('/', \MattCam\Controllers\CameraController::class . ":renderHomepage");
$app->get('/camera/{camera_name}', \MattCam\Controllers\CameraController::class . ":renderHomepage");

$app->post('/camera/ptz', \MattCam\Controllers\CameraController::class . ":doPtz");

$app->group("/api", function(){
    $this->group("/v1", function(){
        $this->get("/ping", \MattCam\Controllers\PingController::class . ':doPing');
    });
});