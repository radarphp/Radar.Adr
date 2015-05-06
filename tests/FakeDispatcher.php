<?php
namespace Radar\Adr;

class FakeDispatcher extends Dispatcher
{
    public function __construct(FakeMiddle $middle)
    {
        $this->middle = $middle;
    }

    public function __invoke() {
        return __METHOD__;
    }
}
