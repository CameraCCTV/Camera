<?php
$environment = array_merge($_ENV, $_SERVER);
ksort($environment);

require_once("vendor/autoload.php");

$cameraName   = $environment['CAMERA_NAME'];
$cameraSource = $environment['CAMERA_SOURCE'];
$service = parse_url($environment['SERVICE_PORT']);
$baseUrl = "http://{$service['host']}:{$service['port']}";
echo "Registering Camera {$cameraName} with service\n";
$registrationJson = [
    'cameraName' => $cameraName,
    'cameraSource' => $cameraSource,
    'audioAllowed' => (!isset($environment['CAMERA_AUDIO_DISABLED'])) ? true : false,
];

$client = new GuzzleHttp\Client();
$res = $client->request('POST', "{$baseUrl}/register-camera.php", [
    'body' => json_encode($registrationJson)
]);


echo $res->getBody();
// {"type":"User"...'
