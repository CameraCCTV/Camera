<?php

namespace MattCam\Controllers;

use MattCam\Exceptions\CameraServiceException;
use MattCam\Services\CameraService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class CameraController extends Controller{

    /** @var Twig */
    protected $renderer;

    /** @var CameraService */
    protected $cameraService;

    protected $environment;

    public function __construct(Twig $renderer, CameraService $cameraService)
    {
        $this->renderer = $renderer;
        $this->cameraService = $cameraService;
        $this->environment = array_merge($_ENV, $_SERVER);
        ksort($this->environment);
    }

    public function renderHomepage(Request $request, Response $response, array $args = [])
    {
        $cameras = $this->cameraService->getListOfCameras();
        $mode = "list";
        if(isset($args['camera_name'])){
            $mode = "single";
            if(!isset($cameras[$args['camera_name']])){
                return $response->withRedirect("/");
            }
            $cameras = [$cameras[$args['camera_name']]];
        }
        return $this->renderer->render(
            $response,
            'cameras/all.html.twig',
            [
                'streaming_service_url' => $this->environment['STREAMING_HOST'],
                'body_class' => "camera_{$mode}",
                'cameras' => $cameras,
            ]
        );
    }

    public function renderSingleCamera(Request $request, Response $response, array $args = [])
    {
        $cameras = $this->cameraService->getListOfCameras();
        $cameras = [$cameras[$args['camera_name']]];
        return $this->renderer->render(
            $response,
            'cameras/all.html.twig',
            [
                'streaming_service_url' => $this->environment['SERVICE_1_ENV_VIRTUAL_HOST'],
                'body_class' => "camera_list",
                'cameras' => $cameras,
            ]
        );
    }

    public function doPtz(Request $request, Response $response, array $args = [])
    {
        $json = [];
        try{
            $this->cameraService->ptzMove($request->getParam('camera'), $request->getParam('action'));
            $json['Status'] = "Okay";
        }catch(CameraServiceException $cameraServiceException){
            $json['Status'] = "Fail";
            $json['Reason'] = $cameraServiceException->getMessage();
        }
        return $response->withJson($json);
    }
}