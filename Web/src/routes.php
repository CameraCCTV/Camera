<?php
// Routes

$app->get('/', \RatCam\Controllers\HomeController::class . ":renderHomepage");

$app->get('/home', \RatCam\Controllers\HomeController::class . ":renderHomepage");

$app->get('/dashboard', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Dashboard");

    // Render index view
    return $this->renderer->render($response, 'dash/dashboard.html.twig', $args);
});

$app->group("/api", function(){
    $this->group("/v1", function(){
        $this->get("/ping", \RatCam\Controllers\PingController::class . ':doPing');
    });
});