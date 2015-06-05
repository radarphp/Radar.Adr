<?php
namespace Radar\Adr\Fake;

class FakeMiddleware
{
    public static $count;

    public function __invoke($request, $response, $next)
    {
        $response->getBody()->write(++ static::$count);
        $response = $next($request, $response);
        $response->getBody()->write(++ static::$count);
        return $response;
    }
}
