<?php
namespace Radar\Adr\Handler;

use Arbiter\ActionFactory;
use Aura\Router\Matcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Radar\Adr\Route;

class RoutingHandler
{
    protected $actionFactory;
    protected $matcher;
    protected $failResponder;

    public function __construct(Matcher $matcher, ActionFactory $actionFactory, $failResponder = null)
    {
        $this->matcher = $matcher;
        $this->actionFactory = $actionFactory;
        $this->failResponder = $failResponder ?: 'Radar\Adr\Responder\RoutingFailedResponder';
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $request = $this->routeRequest($request);
        return $next($request, $response);
    }

    protected function routeRequest(Request $request)
    {
        $route = $this->matcher->match($request);

        if (! $route) {
            return $request->withAttribute(
                'radar/adr:action',
                $this->actionFactory->newInstance(
                    null,
                    [$this->matcher, 'getFailedRoute'],
                    $this->failResponder
                )
            );
        }

        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        return $request->withAttribute(
            'radar/adr:action',
            $this->actionFactory->newInstance(
                $route->input,
                $route->domain,
                $route->responder
            )
        );
    }
}
