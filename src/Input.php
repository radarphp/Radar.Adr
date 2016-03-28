<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Psr\Http\Message\ServerRequestInterface as Request;

class Input
{
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
