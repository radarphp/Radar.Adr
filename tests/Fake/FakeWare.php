<?php
namespace Radar\Adr\Fake;

class FakeWare
{
    public static $count;

    public function __invoke($request, $response, $next)
    {
        static::$count ++;
        $response->getBody()->write(static::$count);
        return $next($request, $response);
    }
}
