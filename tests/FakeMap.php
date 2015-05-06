<?php
namespace Radar\Adr;

use Radar\Adr\Router\Map;
use Radar\Adr\Router\Route;

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
