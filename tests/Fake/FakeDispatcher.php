<?php
namespace Radar\Adr\Fake;

use Radar\Adr\Dispatcher;

class FakeDispatcher extends Dispatcher
{
    public function __construct(FakeMiddle $middle)
    {
        $this->middle = $middle;
    }

    public function run() {
        return __METHOD__;
    }
}
