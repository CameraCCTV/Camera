<?php

namespace RatCam\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class HomeController extends Controller{

    /** @var Twig */
    protected $renderer;

    public function __construct(Twig $renderer)
    {
        $this->renderer = $renderer;
    }

    public function renderHomepage(Request $request, Response $response, array $args = [])
    {
        return $this->renderer->render($response, 'home/home.html.twig', $args);
    }
}