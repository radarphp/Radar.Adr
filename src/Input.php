<?php
namespace Radar\Adr;

use Psr\Http\Message\ServerRequestInterface as Request;

class Input
{
    public function __invoke(Request $request)
    {
        return [
            array_merge(
                (array) $request->getQueryParams(),
                (array) $request->getAttributes(),
                (array) $request->getParsedBody(),
                (array) $request->getUploadedFiles()
            )
        ];
    }
}
