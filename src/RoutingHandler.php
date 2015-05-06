<?php
namespace Radar\Adr;

use Aura\Router\Matcher;
use Psr\Http\Message\ServerRequestInterface;

class RoutingHandler
{
    // inject a proto-route and modify that instead of the failed route
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
            $route->domain([$this->matcher, 'getFailedRoute']);
            $route->responder('Radar\Adr\RoutingFailedResponder');
        }
        return $route;
    }
}
