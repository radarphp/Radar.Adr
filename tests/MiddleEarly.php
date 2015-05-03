<?php
namespace Radar\Adr;

class MiddleEarly
{
    public function __invoke(&$request, &$response)
    {
        $response->getBody()->write('Early exit in middle');
        return $response;
    }
}
