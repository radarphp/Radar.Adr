<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr\Handler;

use Arbiter\ActionFactory;
use Aura\Router\Matcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Radar\Adr\Route;

/**
 *
 * Middleware to route (but not dispatch) the Request.
 *
 * @package radar/adr
 *
 */
class RoutingHandler
{
    protected $actionFactory;
    protected $matcher;
    protected $failResponder;

    public function __construct(
        Matcher $matcher,
        ActionFactory $actionFactory,
        $failResponder = 'Radar\Adr\Responder\RoutingFailedResponder'
    ) {
        $this->matcher = $matcher;
        $this->actionFactory = $actionFactory;
        $this->failResponder = $failResponder;
    }

    /**
     *
     * Adds the Action specification for the Route to the Request.
     *
     * @param Request $request The HTTP request object.
     *
     * @param Request $request The HTTP response object.
     *
     * @param callable $next The next middleware decorator.
     *
     * @return Response
     *
     */
    public function __invoke(
        Request $request,
        Response $response,
        callable $next
    ) {
        $route = $this->matcher->match($request);
        $request = $this->addRouteToRequest($route, $request);
        return $next($request, $response);
    }

    /**
     *
     * Adds the route information to the Request.
     *
     * @param mixed $route The route result.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Request with the route information.
     *
     */
    protected function addRouteToRequest($route, Request $request)
    {
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
