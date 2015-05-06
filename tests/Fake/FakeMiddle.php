<?php
namespace Radar\Adr\Fake;

use Radar\Adr\Middle;

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
