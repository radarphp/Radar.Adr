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

class ActionHandler extends Arbiter
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $action = $request->getAttribute('radar/adr:action');
        $request = $request->withoutAttribute('radar/adr:action');
        $response = $this->handle($action, $request, $response);
        return $next($request, $response);
    }
}
