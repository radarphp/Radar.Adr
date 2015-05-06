<?php
namespace Radar\Adr;

class FakeMiddle extends Middle
{
    public function __construct()
    {
        // no need for factory
    }

    public function __get($key)
    {
        return $this->$key;
    }
}
