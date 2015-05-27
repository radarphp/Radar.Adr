<?php
namespace Radar\Adr;

use Psr\Http\Message\ServerRequestInterface;

class Input
{
    public function __invoke(ServerRequestInterface $request)
    {
        $type   = $request->getHeader('Content-Type');
        $method = $request->getMethod();

        if (
            'GET' != $method && 
            ! empty($type)   &&     
            'application/json' == strtolower($type[0])
        ) {
            $body    = (string) $request->getBody();
            $request = $request->withParsedBody(json_decode($body));
        }

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
