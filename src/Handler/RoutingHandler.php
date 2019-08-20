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
use Radar\Adr\Responder\RoutingFailedResponder;
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

    /**
     * Request Attribute containing the route
     */
    const ROUTE_ATTRIBUTE = 'radar/adr:route';

    /**
     *
     * A factory to create Action objects.
     *
     * @var ActionFactory
     *
     */
    protected $actionFactory;

    /**
     *
     * The Responder class to use when there is no matching route.
     *
     * @var string
     *
     */
    protected $failResponder;

    /**
     *
     * A route matcher.
     *
     * @var Matcher
     *
     */
    protected $matcher;

    /**
     *
     * Constructor.
     *
     * @param Matcher $matcher A route matcher.
     *
     * @param ActionFactory $actionFactory An factory to create Action objects.
     *
     * @param string $failResponder The Responder class to use when there is no
     * matching route.
     *
     */
    public function __construct(
        Matcher $matcher,
        ActionFactory $actionFactory,
        $failResponder = RoutingFailedResponder::class
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
     * @param Response $response The HTTP response object.
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
     * Adds the Route and Action information to the Request.
     *
     * @param mixed $route The route matching the request (if any).
     *
     * @param Request $request The HTTP request object.
     *
     * @return Request with the Route and Action information.
     *
     */
    protected function addRouteToRequest($route, Request $request)
    {
        if (! $route) {
            return $request
                ->withAttribute(self::ROUTE_ATTRIBUTE, false)
                ->withAttribute(
                    ActionHandler::ACTION_ATTRIBUTE,
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

        return $request
            ->withAttribute(self::ROUTE_ATTRIBUTE, $route)
            ->withAttribute(
                ActionHandler::ACTION_ATTRIBUTE,
                $this->actionFactory->newInstance(
                    $route->input,
                    $route->domain,
                    $route->responder
                )
            );
    }
}
