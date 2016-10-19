<?php
namespace Radar\Adr\Fake;

use Zend\Diactoros\Response;

class FakeResponseMiddleware
{

    public function __invoke($request, $next)
    {
        return new Response;
    }
}
