<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware\Handler;

use Arbiter\ActionHandler as Arbiter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 * Dispatches to the Action stored in the `radar/adr:action` Request attribute.
 *
 * @package radar/middleware
 *
 */
class ActionHandler extends Arbiter
{
    /**
     *
     * Dispatches to the Action stored in the `radar/adr:action` Request
     * attribute.
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
        $action = $request->getAttribute('radar/adr:action');
        $request = $request->withoutAttribute('radar/adr:action');
        $response = $this->handle($action, $request, $response);
        return $next($request, $response);
    }
}
