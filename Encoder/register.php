<?php
$environment = array_merge($_ENV, $_SERVER);
ksort($environment);

require_once("vendor/autoload.php");

$cameraName   = $environment['CAMERA_NAME'];
$cameraSource = $environment['CAMERA_SOURCE'];
$service = parse_url($environment['SERVICE_HOST']);
$baseUrl = "http://{$service['host']}:{$service['port']}";
echo "Registering Camera {$cameraName} with service\n";
$registrationJson = [
    'cameraName'   => $cameraName,
    'cameraSource' => $cameraSource,
    'cameraSoap'    => isset($environment['CAMERA_SOAP']) ? $environment['CAMERA_SOAP'] : false,
    'audioAllowed' => (!isset($environment['CAMERA_AUDIO_DISABLED']) && strtolower($environment['CAMERA_AUDIO_DISABLED']) == "yes") ? false : true,
    'ptzAllowed'   => (isset($environment['CAMERA_ENABLE_PTZ'])     && strtolower($environment['CAMERA_ENABLE_PTZ'])     == "yes") ? true : false,
];

$client = new GuzzleHttp\Client();
$res = $client->request('POST', "{$baseUrl}/register-camera.php", [
    'body' => json_encode($registrationJson)
]);


echo $res->getBody();
// {"type":"User"...'

if(isset($environment['CAMERA_SOAP'])) {
    $soapPath = parse_url($environment['CAMERA_SOAP']);

    $ponvif = new \ponvif();

    $ponvif->setUsername('guest');
    $ponvif->setPassword('guest');
    $ponvif->setDeviceUri($environment['CAMERA_SOAP'] . '/onvif/device_service');
    $ponvif->setIPAddress($soapPath['host']);

    $ponvif->initialize();

    $ponvif->core_SetSystemDateAndTime();
}