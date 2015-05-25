<?php
namespace Radar\Adr;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dispatcher
{
    protected $handlers;

    public function __construct(Handlers $handlers)
    {
        $this->handlers = $handlers;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        try {
            $middle = $this->handlers->shiftMiddle();
            return $middle($request, $response, $this);
        } catch (Exception $e) {
            $execptionHandler = $this->handlers->getExceptionHandler();
            return $execptionHandler($request, $response, $e);
        }
    }
}
