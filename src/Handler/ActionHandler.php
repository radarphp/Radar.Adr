<?php
namespace Radar\Adr\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Factory;
use Radar\Adr\Router\Route;

class ActionHandler
{
    protected $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Route $route
    ) {
        $factory = $this->factory;
        $responder = $factory($route->responder);

        if ($route->domain) {
            $payload = $this->domain($route, $request);
            return $responder($request, $response, $payload);
        }

        return $responder($request, $response);
    }

    protected function domain(Route $route, ServerRequestInterface $request)
    {
        $factory = $this->factory;
        $domain = $factory($route->domain);

        $input = [];
        if ($route->input) {
            $input = $factory($route->input);
            $input = (array) $input($request);
            return call_user_func_array($domain, $input);
        }

        return $domain();
    }
}
