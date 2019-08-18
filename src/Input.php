<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 * A generic input marshal.
 *
 * @package radar/middleware
 *
 */
class Input
{
    /**
     *
     * Returns an array of input built from Request parameters and attributes,
     * suitable for passing to `call_user_func_array()` as a single argument.
     *
     * @param Request $request The HTTP request.
     *
     * @return array An array with one element: the array of combined Request
     * parameters and attributes.
     *
     */
    public function __invoke(Request $request)
    {
        // cf. EGPCS, where Files is part of Post,
        // and Attributes is part of Server
        return [
            array_replace(
                (array) $request->getQueryParams(),
                (array) $request->getParsedBody(),
                (array) $request->getUploadedFiles(),
                (array) $request->getCookieParams(),
                (array) $request->getAttributes()
            )
        ];
    }
}
