<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
//$container['renderer'] = function (\Slim\Container $container) {
//    $settings = $container->get('settings')['renderer'];
//    return new Slim\Views\PhpRenderer($settings['template_path']);
//};

$container['renderer'] = function ($c) {
    $view = new \Slim\Views\Twig(
        APP_ROOT . '/views/',
        [
            'cache' => false,
            'debug' => true
        ]
    );

    // Instantiate and add Slim specific extension
    $view->addExtension(
        new Slim\Views\TwigExtension(
            $c['router'],
            $c['request']->getUri()
        )
    );

    // Added Twig_Extension_Debug to enable twig dump() etc.
    $view->addExtension(
        new \Twig_Extension_Debug()
    );

    $view->addExtension(new \Twig_Extensions_Extension_Text());

    return $view;
};

// monolog
$container['logger'] = function (\Slim\Container $container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// Database..
$container['database'] = function(\Slim\Container $container){
    $settings = $container->get('settings')['database'];

    // Database Settings
    $dbConfig = array(
        'db_type'     => $settings['technology'],
        'db_hostname' => $settings['hostname'],
        'db_port'     => $settings['port'],
        'db_username' => $settings['username'],
        'db_password' => $settings['password'],
        'db_database' => $settings['database']
    );
    $database = new \Thru\ActiveRecord\DatabaseLayer($dbConfig);
    return $database;
};

$container[\RatCam\Services\PlugService::class] = function(\Slim\Container $container){
    $plugService = new \RatCam\Services\PlugService();
    return $plugService;
};

$container[\RatCam\Services\UserService::class] = function(\Slim\Container $container){
    return new \RatCam\Services\UserService();
};

$container[\RatCam\Controllers\PlugController::class] = function(\Slim\Container $container){
    return new \RatCam\Controllers\PlugController(
        $container->get(\RatCam\Services\PlugService::class),
        $container->get("logger")
    );
};

$container[\RatCam\Controllers\UserController::class] = function(\Slim\Container $container){
    return new \RatCam\Controllers\UserController(
        $container->get(\RatCam\Services\UserService::class),
        $container->get("logger")
    );
};

$container[\RatCam\Controllers\HomeController::class] = function(\Slim\Container $container){
    return new \RatCam\Controllers\HomeController(
        $container->get("renderer")
    );
};