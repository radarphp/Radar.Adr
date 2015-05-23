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
        callable $next
    ) {
        $route = $request->getAttribute('radar/adr:route');
        $request = $request->withoutAttribute('radar/adr:route');

        $responder = $this->factory->invokable($route->responder);
        if ($route->domain) {
            $payload = $this->domain($route, $request);
            return $responder($request, $response, $payload);
        }

        $response = $responder($request, $response);
        return $next($request, $response);
    }

    protected function domain(Route $route, ServerRequestInterface $request)
    {
        $domain = $this->factory->invokable($route->domain);

        $input = [];
        if ($route->input) {
            $input = $this->factory->invokable($route->input);
            $input = (array) $input($request);
            return call_user_func_array($domain, $input);
        }

        return $domain();
    }
}
