<?php
namespace Radar\Adr\Handler;

use Aura\Router\Matcher;
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

    public function __invoke(ServerRequestInterface $request)
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
