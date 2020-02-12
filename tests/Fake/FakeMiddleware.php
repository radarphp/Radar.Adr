<?php
namespace Radar\Adr\Fake;

class FakeMiddleware
{
    public static $count;

    public function __invoke($request, $response, $next)
    {
        $response->getBody()->write((string) ++ static::$count);
        $response = $next($request, $response);
        $response->getBody()->write((string) ++ static::$count);
        return $response;
    }
}
