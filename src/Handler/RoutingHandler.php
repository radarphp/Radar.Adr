<?php
namespace Radar\Adr\Handler;

use Aura\Router\Matcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Radar\Adr\Action;
use Radar\Adr\Router\Route;

class RoutingHandler
{
    protected $action;
    protected $matcher;

    public function __construct(Matcher $matcher, Action $action)
    {
        $this->matcher = $matcher;
        $this->action = $action;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $request = $this
            ->routeRequest($request)
            ->withAttribute('radar/adr:action', $this->action);
        return $next($request, $response);
    }

    protected function routeRequest(Request $request)
    {
        $route = $this->matcher->match($request);
        if (! $route) {
            return $this->noAction($request);
        }
        return $this->action($request, $route);
    }

    protected function noAction(Request $request)
    {
        $this->action
            ->setInput(null)
            ->setDomain([$this->matcher, 'getFailedRoute'])
            ->setResponder('Radar\Adr\Responder\RoutingFailedResponder');
        return $request;
    }

    protected function action(Request $request, Route $route)
    {
        $this->action
            ->setInput($route->input)
            ->setDomain($route->domain)
            ->setResponder($route->responder);

        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        return $request;
    }
}
