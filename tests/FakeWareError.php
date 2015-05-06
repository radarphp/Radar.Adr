<?php
namespace Radar\Adr;

class FakeWareError
{
    public function __invoke(&$request, &$response)
    {
        throw new Exception('Error in middle');
    }
}
