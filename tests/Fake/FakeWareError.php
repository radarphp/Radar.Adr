<?php
namespace Radar\Adr\Fake;

use Exception;

class FakeWareError
{
    public function __invoke(&$request, &$response)
    {
        throw new Exception('Error in middle');
    }
}
