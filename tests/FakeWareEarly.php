<?php
namespace Radar\Adr;

class FakeWareEarly
{
    public function __invoke(&$request, &$response)
    {
        $response->getBody()->write('Early exit in middle');
        return $response;
    }
}
