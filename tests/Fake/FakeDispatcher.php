<?php
namespace Radar\Adr\Fake;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Dispatcher;

class FakeDispatcher extends Dispatcher
{
    public function __construct(FakeHandlers $handlers)
    {
        $this->handlers = $handlers;
    }

    public function __invoke(
        Request $request,
        Response $response
    ) {
        return __METHOD__;
    }
}
