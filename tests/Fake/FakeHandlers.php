<?php
namespace Radar\Adr\Fake;

use Radar\Adr\Handlers;

class FakeHandlers extends Handlers
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
