<?php
namespace Radar\Adr;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Dispatcher
{
    protected $handlers;

    public function __construct(Handlers $handlers)
    {
        $this->handlers = $handlers;
    }

    public function __invoke(
        Request $request,
        Response $response
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
