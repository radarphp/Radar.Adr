<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr\Handler;

use Arbiter\ActionHandler as Arbiter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 * Dispatches to the Action stored in the `radar/adr:action` Request attribute.
 *
 * @package radar/adr
 *
 */
class ActionHandler extends Arbiter
{

    /**
     * Request Attribute containing the action
     */
    const ACTION_ATTRIBUTE = 'radar/adr:action';

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
        $action = $request->getAttribute(self::ACTION_ATTRIBUTE);
        $request = $request->withoutAttribute(self::ACTION_ATTRIBUTE);
        $response = $this->handle($action, $request, $response);
        return $next($request, $response);
    }
}
