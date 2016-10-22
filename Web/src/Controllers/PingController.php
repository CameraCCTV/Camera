<?php

namespace MattCam\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class PingController extends Controller{
    public function doPing(Request $request, Response $response, array $args = [])
    {
        $this->jsonResponse([
            'Status' => 'Okay'
        ], $request, $response);
    }
}