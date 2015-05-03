<?php
namespace Radar\Adr;

class Middle
{
    public static $count;

    public function __invoke(&$request, &$response)
    {
        static::$count ++;
        $request = $request->withAttribute('radar/adr:test-middle', static::$count);
        return static::$count;
    }
}
