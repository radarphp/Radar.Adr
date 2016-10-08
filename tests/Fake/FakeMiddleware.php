<?php
namespace Radar\Adr\Fake;

use Zend\Diactoros\Response;

class FakeMiddleware
{
    public static $count;

    public function __invoke($request, $next)
    {
        $response = $next($request);
        $response->getBody()->write(++ static::$count);
        return $response;
    }
}
