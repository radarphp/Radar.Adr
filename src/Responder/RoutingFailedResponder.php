<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr\Responder;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Route;

/**
 *
 * A Responder for when there is no matching route.
 *
 * @package radar/adr
 *
 */
class RoutingFailedResponder
{
    /**
     *
     * The HTTP request.
     *
     * @var Request
     *
     */
    protected $request;

    /**
     *
     * The HTTP response.
     *
     * @var Response
     *
     */
    protected $response;

    /**
     *
     * The closest route that failed to match.
     *
     * @var Route
     *
     */
    protected $failedRoute;

    /**
     *
     * Builds the Response for a failure-to-route.
     *
     * @param Request $request The HTTP request object.
     *
     * @param Response $response The HTTP response object.
     *
     * @param Route The closest route that failed to match.
     *
     * @return Response
     *
     */
    public function __invoke(
        Request $request,
        Response $response,
        Route $failedRoute
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->failedRoute = $failedRoute;
        $method = $this->getMethodForFailedRoute();
        $this->$method();
        return $this->response;
    }

    /**
     *
     * Returns the Responder method to call, based on the failed route.
     *
     */
    protected function getMethodForFailedRoute()
    {
        switch ($this->failedRoute->failedRule) {
            case 'Aura\Router\Rule\Allows':
                return 'methodNotAllowed';
            case 'Aura\Router\Rule\Accepts':
                return 'notAcceptable';
            case 'Aura\Router\Rule\Host':
            case 'Aura\Router\Rule\Path':
                return 'notFound';
            default:
                return 'other';
        }
    }

    /**
     *
     * Builds the Response when the failed route method was not allowed.
     *
     */
    protected function methodNotAllowed()
    {
        $this->response = $this->response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->failedRoute->allows))
            ->withHeader('Content-Type', 'application/json');

        $this->response->getBody()->write(json_encode($this->failedRoute->allows));
    }

    /**
     *
     * Builds the Response when the failed route could not accept the media type.
     *
     */
    protected function notAcceptable()
    {
        $this->response = $this->response
            ->withStatus(406)
            ->withHeader('Content-Type', 'application/json');

        $this->response->getBody()->write(json_encode($this->failedRoute->accepts));
    }

    /**
     *
     * Builds the Response when the failed route host or path was not found.
     *
     */
    protected function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->response->getBody()->write('404 Not Found');
    }

    /**
     *
     * Builds the Response when routing failed for some other reason.
     *
     */
    protected function other()
    {
        $this->response = $this->response->withStatus(500);

        $message = "Route " . $this->failedRoute->name
            . " failed rule " . $this->failedRoute->failedRule;

        $this->response->getBody()->write($message);
    }
}
