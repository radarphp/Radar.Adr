<?php
namespace Radar\Adr\Responder;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Router\Route;

class RoutingFailedResponder
{
    protected $request;
    protected $response;
    protected $failedRoute;

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Route $failedRoute
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->failedRoute = $failedRoute;
        $this->exec();
        return $this->response;
    }

    protected function exec()
    {
        switch ($this->failedRoute->failedRule) {
            case 'Aura\Router\Rule\Allows':
                return $this->methodNotAllowed();
            case 'Aura\Router\Rule\Accepts':
                return $this->notAcceptable();
            case 'Aura\Router\Rule\Host':
            case 'Aura\Router\Rule\Path':
                return $this->notFound();
            default:
                return $this->other();
        }
    }

    protected function methodNotAllowed()
    {
        $this->response = $this->response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->failedRoute->allows))
            ->withHeader('Content-Type', 'application/json');

        $this->response->getBody()->write(json_encode($this->failedRoute->allows));
    }

    protected function notAcceptable()
    {
        $this->response = $this->response
            ->withStatus(406)
            ->withHeader('Content-Type', 'application/json');

        $this->response->getBody()->write(json_encode($this->failedRoute->accepts));
    }

    protected function notFound()
    {
        $this->response = $this->response
            ->withStatus(404);

        $this->response->getBody()->write('404 Not Found');
    }

    protected function other()
    {
        $this->response = $this->response
            ->withStatus(500);

        $message = "Route " . $this->failedRoute->name
            . " failed rule " . $this->failedRoute->failedRule;

        $this->response->getBody()->write($message);
    }
}
