<?php

define("APP_ROOT", __DIR__);
define("APP_START", microtime(true));

// PHP Settings
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(120);
ini_set('memory_limit', '32M');
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = APP_ROOT . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require APP_ROOT . '/vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require APP_ROOT . '/src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require APP_ROOT . '/src/dependencies.php';

// Register middleware
require APP_ROOT . '/src/middleware.php';

// Register routes
require APP_ROOT . '/src/routes.php';

// Force init DB.
$app->getContainer()->get("database");


