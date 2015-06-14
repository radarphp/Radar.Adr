<?php
namespace Radar\Adr\Fake;

use Aura\Router\Map;
use Radar\Adr\Route;

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
