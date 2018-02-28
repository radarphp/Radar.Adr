<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware\Handler;

use Arbiter\ActionFactory;
use Aura\Router\Matcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Radar\Middleware\ActionSpecs;

/**
 *
 * Middleware to route (but not dispatch) the Request.
 *
 * @package radar/middleware
 *
 */
class RoutingHandler implements MiddlewareInterface
{
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
     * @param Matcher $matcher
     *
     * @param ActionFactory $actionFactory An factory to create Action objects.
     *
     * @param string $failResponder The Responder class to use when there is no
     * matching route.
     */
    public function __construct(
        Matcher $matcher,
        ActionFactory $actionFactory,
        $failResponder = 'Radar\Middleware\Responder\RoutingFailedResponder'
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
     * @param RequestHandlerInterface $handler The handler middleware decorator.
     *
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $route = $this->matcher->match($request);

        $request = $this->addRouteToRequest($route, $request);
        return $handler->handle($request);
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
    protected function addRouteToRequest($route, Request $request): Request
    {

        if (! $route) {
            return $request
                ->withAttribute('radar/adr:route', false)
                ->withAttribute(
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

        $specs = new ActionSpecs($route->handler);

        return $request
            ->withAttribute('radar/adr:route', $route)
            ->withAttribute(
                'radar/adr:action',
                $this->actionFactory->newInstance(
                    $specs->input,
                    $specs->domain,
                    $specs->responder
                )
            );
    }
}
