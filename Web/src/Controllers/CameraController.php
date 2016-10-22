<?php

namespace RatCam\Controllers;

use RatCam\Exceptions\CameraServiceException;
use RatCam\Services\CameraService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class CameraController extends Controller{

    /** @var Twig */
    protected $renderer;

    /** @var CameraService */
    protected $cameraService;

    public function __construct(Twig $renderer, CameraService $cameraService)
    {
        $this->renderer = $renderer;
        $this->cameraService = $cameraService;
    }

    public function renderHomepage(Request $request, Response $response, array $args = [])
    {
        $cameras = $this->cameraService->getListOfCameras();
        return $this->renderer->render(
            $response,
            'home/home.html.twig',
            [
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