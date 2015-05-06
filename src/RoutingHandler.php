<?php
namespace Radar\Adr;

use Aura\Router\Matcher;
use Psr\Http\Message\ServerRequestInterface;

class RoutingHandler
{
    public function __construct(Matcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $route = $this->matcher->match($request);
        if (! $route) {
            $route = clone $this->matcher->getFailedRoute();
            $route->input(null);
            $route->domain([$this, 'getFailedRoute']);
            $route->responder('Radar\Adr\RoutingFailedResponder');
        }
        return $route;
    }

    public function getFailedRoute()
    {
        return $this->matcher->getFailedRoute();
    }
}
