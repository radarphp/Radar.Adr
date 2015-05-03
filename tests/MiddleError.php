<?php
namespace Radar\Adr;

class MiddleError
{
    public function __invoke(&$request, &$response)
    {
        throw new Exception('Error in middle');
    }
}
