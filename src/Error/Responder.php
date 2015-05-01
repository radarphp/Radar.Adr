<?php
namespace Radar\Adr\Error;

use Aura\Payload\Payload;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Exception\RoutingFailed;

class Responder
{
    protected $request;
    protected $response;
    protected $payload;

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Payload $payload
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->payload = $payload;
        $this->handleException($this->payload->getOutput());
        return $this->response;
    }

    protected function handleException(Exception $e)
    {
        switch (get_class($e)) {
            case 'Radar\Adr\Exception\RoutingFailed':
                return $this->routingFailed($e);
            default:
                return $this->unknown($e);
        }
    }

    protected function routingFailed(RoutingFailed $e)
    {
        $route = $e->getFailedRoute();
        switch ($route->failedRule) {
            case 'Aura\Router\Rule\Allows':
                return $this->methodNotAllowed($route);
            case 'Aura\Router\Rule\Accepts':
                return $this->notAcceptable($route);
            default:
                return $this->notFound($route);
        }
    }

    protected function notFound($route)
    {
        $this->response = $this->response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/plain');

        $this->response->getBody()->write('404 Not Found');
    }

    protected function methodNotAllowed($route)
    {
        $this->response = $this->response
            ->withStatus(405)
            ->withHeader('Allow', implode(',', $route->allows))
            ->withHeader('Content-Type', 'text/plain');

        $this->response->getBody()->write('405 Method Not Allowed');
    }

    protected function notAcceptable($route)
    {
        $this->response = $this->response
            ->withStatus(406)
            ->withHeader('Content-Type', 'application/json');

        $this->response->getBody()->write(json_encode($route->accepts));
    }

    protected function unknown($e)
    {
        $this->response = $this->response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/plain');

        $this->response->getBody()->write($e->getMessage());
    }
}
