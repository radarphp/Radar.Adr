<?php
namespace Radar\Adr\Handler;

use Aura\Router\Matcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Radar\Adr\Router\Route;

class RoutingHandler
{
    protected $matcher;
    protected $route;

    public function __construct(Matcher $matcher, Route $route)
    {
        $this->matcher = $matcher;
        $this->route = $route;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $request = $this->routeRequest($request);
        return $next($request, $response);
    }

    protected function routeRequest(ServerRequestInterface $request)
    {
        $route = $this->route($request);
        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }
        $request = $request->withAttribute('radar/adr:route', $route);
        return $request;
    }

    protected function route(ServerRequestInterface $request)
    {
        $route = $this->matcher->match($request);
        if (! $route) {
            $route = clone $this->route;
            $route->input(null);
            $route->domain([$this->matcher, 'getFailedRoute']);
            $route->responder('Radar\Adr\Responder\RoutingFailedResponder');
        }
        return $route;
    }
}
