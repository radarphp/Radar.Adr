<?php
namespace Radar\Adr\Fake;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Dispatcher;

class FakeDispatcher extends Dispatcher
{
    public function __construct(FakeHandlers $handlers)
    {
        $this->handlers = $handlers;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        return __METHOD__;
    }
}
