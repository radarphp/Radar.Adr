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
     *
     * Dispatches to the Action stored in the `radar/adr:action` Request
     * attribute.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response
     *
     */
    public function __invoke(
        Request $request
    ) {
        $action = $request->getAttribute('radar/adr:action');
        $request = $request->withoutAttribute('radar/adr:action');
        return $this->handle($action, $request);
    }
}
