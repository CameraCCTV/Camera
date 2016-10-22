<?php

namespace MattCam\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller{
    
    public function jsonResponse($json, Request $request, Response $response)
    {
        if (strtolower($json['Status']) != "okay") {
            $response = $response->withStatus(400);
        } else {
            $response = $response->withStatus(200);
        }
        $json['RunTime'] = number_format(microtime(true) - APP_START, 4);
        $response = $response->withJson($json);
        return $response;

    }

    public function jsonResponseException(\Exception $e, Request $request, Response $response)
    {
        return $this->jsonResponse(
            [
                'Status' => 'FAIL',
                'Reason' => $e->getMessage(),
            ],
            $request,
            $response
        );
    }
}