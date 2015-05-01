<?php
namespace Radar\Adr;

use Psr\Http\Message\ServerRequestInterface;

class Input
{
    public function __invoke(ServerRequestInterface $request)
    {
        return [
            array_merge(
                $request->getQueryParams(),
                $request->getAttributes(),
                $request->getParsedBody(),
                $request->getUploadedFiles()
            )
        ];
    }
}
