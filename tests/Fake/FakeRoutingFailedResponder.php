<?php

namespace Radar\Adr\Responder;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Route;

use Radar\Adr\Responder\RoutingFailedResponder;

class FakeRoutingFailedResponder extends RoutingFailedResponder
{
}
