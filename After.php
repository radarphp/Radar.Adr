<?php
namespace Radar\Adr;

class After
{
    public static $count;
    public static $error;
    public static $early;

    public function __invoke(&$request, &$response)
    {
        static::$count ++;

        if (static::$count == static::$error) {
            throw new Exception('Error on before ' . static::$count);
        }

        $request = $request->withAttribute('radar/adr:test-before', static::$count);

        if (static::$count == static::$early) {
            $response->getBody()->write('Early exit');
            return $response;
        }
    }
}
