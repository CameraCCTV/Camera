<?php

require_once("../bootstrap.php");

$configDir = "/app/conf/";

$cameras = [];
foreach(scandir($configDir) as $item){
    switch($item){
        case substr($item,-4,4) == ".yml":
            $configFile = $configDir . $item;
            $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($configFile));
            $cameras[] = $config;
            break;
        default:
    }
}

echo json_encode([
    'cameras' => $cameras
], JSON_PRETTY_PRINT);