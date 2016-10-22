<?php

namespace MattCam\Controllers;

use Monolog\Logger;
use MattCam\Services\UserService;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller{

    /** @var UserService */
    private $userService;
    /** @var Logger */
    private $logger;

    public function __construct(UserService $userService, Logger $logger)
    {
        $this->userService = $userService;
        $this->logger = $logger;
    }

    public function showLogin(Request $request, Response $response, array $args = [])
    {
        $body = $request->getBody()->getContents();
        $body = json_decode($body);
        $this->logger->info("Alive! Device {$body->hardware_id} has checked in.");

        $plug = $this->plugService->findPlugByHardwareId($body->hardware_id);
        $beat = $this->plugService->beat($plug);
        if(isset($body->volts)) {
            $beat->volts = intval($body->volts) / 1000;
        }
        if(isset($body->heap)) {
            $beat->heap = intval($body->heap);
        }
        if(isset($body->firmware)) {
            $beat->firmware = $body->firmware;
        }

        $beat->ip_address = $request->getServerParams()['REMOTE_ADDR'];
        $beat->save();

        $this->jsonResponse([
            'Status' => 'Okay',
            'HeartbeatInterval' => 10
        ], $request, $response);
    }
    
}