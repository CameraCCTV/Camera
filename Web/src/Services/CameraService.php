<?php

namespace MattCam\Services;

use GuzzleHttp\Client as GuzzleClient;
use MattCam\Exceptions\CameraServiceException;

class CameraService extends Service
{

    public function getListOfCameras()
    {
        $environment = array_merge($_ENV, $_SERVER);
        ksort($environment);
        $service = parse_url($environment['SERVICE_PORT']);
        $client = new GuzzleClient(['base_uri' => "http://{$service['host']}:{$service['port']}"]);
        $res = $client->request('GET', "/get-cameras.php");

        $response = json_decode($res->getBody(), true);

        if ($res->getStatusCode() == 200) {
            $cameras = [];
            foreach ($response['cameras'] as $camera) {
                $cameras[$camera['cameraName']] = $camera;
            }
            return $cameras;
        }
        return false;
    }

    public function ptzMove($camera, $action)
    {
        $cameras = $this->getListOfCameras();
        if (!isset($cameras[$camera])) {
            throw new CameraServiceException("Camera \"{$camera}\" is not available.");
        }
        if (!$cameras[$camera]['ptzAllowed']) {
            throw new CameraServiceException("PTZ is not allowed on \"{$camera}\", or is not available.");
        }
        if (!$cameras[$camera]['cameraSoap']) {
            throw new CameraServiceException("CAMERA_SOAP is not configured on \"{$camera}\", or is not available.");
        }
        if (!in_array($action, ['up', 'down', 'left', 'right'])) {
            throw new CameraServiceException("PTZ action {$action} is not a valid action.");
        }
        $soapPath = parse_url($cameras[$camera]['cameraSoap']);
        #!\Kint::dump($cameras[$camera], $soapPath);
        try {
            $ponvif = new \ponvif();

            $ponvif->setUsername('guest');
            $ponvif->setPassword('guest');
            $ponvif->setDeviceUri($cameras[$camera]['cameraSoap'] . '/onvif/device_service');
            $ponvif->setIPAddress($soapPath['host']);

            $ponvif->initialize();

            if ($ponvif->isFault($sources = $ponvif->getSources())) die("Error getting sources");

            $profileToken = $sources[0][0]['profiletoken'];
            $ptzNodeToken = $sources[0][0]['ptz']['nodetoken'];

            $mediaUri = $ponvif->media_GetStreamUri($profileToken);
            #\Kint::dump($profileToken, $ptzNodeToken, $mediaUri);

            switch ($action) {
                case 'left':
                    $ponvif->ptz_RelativeMove($profileToken, -5, 0, 5, 0);
                    break;
                case 'right':
                    $ponvif->ptz_RelativeMove($profileToken, 5, 0, 5, 0);
                    break;
                case 'up':
                    $ponvif->ptz_RelativeMove($profileToken, 0, 5, 0, 5);
                    break;
                case 'down':
                    $ponvif->ptz_RelativeMove($profileToken, 0, -5, 0, 5);
                    break;
            }
        }catch(\Exception $e){
            throw new CameraServiceException("ponvif library fault: " . $e->getMessage());
        }
        return true;
    }

}