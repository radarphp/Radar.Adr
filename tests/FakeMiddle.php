<?php
namespace Radar\Adr;

class FakeMiddle extends Middle
{
    public function __construct()
    {
        // do nothing
    }

    public function __get($key)
    {
        return $this->$key;
    }
}
