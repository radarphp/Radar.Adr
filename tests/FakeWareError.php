<?php
namespace Radar\Adr;

use Exception;

class FakeWareError
{
    public function __invoke(&$request, &$response)
    {
        throw new Exception('Error in middle');
    }
}
