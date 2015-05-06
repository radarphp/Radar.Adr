<?php
namespace Radar\Adr;

use Aura\Router\Map;

class FakeMap extends Map
{
    public function __construct(Route $route)
    {
    }

    public function fakeMapMethod()
    {
        return __METHOD__;
    }
}
