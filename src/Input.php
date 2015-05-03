<?php
namespace Radar\Adr;

use Psr\Http\Message\ServerRequestInterface;

class Input
{
    public function __invoke(ServerRequestInterface $request)
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
