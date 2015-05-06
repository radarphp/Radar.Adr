<?php
namespace Radar\Adr;

class FakeDispatcher extends Dispatcher
{
    public function __construct()
    {
        // do nothing
    }

    public function __invoke(
        Middle $middle,
        $routingHandler,
        $sendingHandler,
        $exceptionHandler
    ) {
        return __METHOD__;
    }
}
