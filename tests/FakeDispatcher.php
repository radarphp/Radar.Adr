<?php
namespace Radar\Adr;

class FakeDispatcher implements DispatcherInterface
{
    public function __invoke(
        array $middle,
        $routingHandler,
        $sendingHandler,
        $exceptionHandler
    ) {
        return __METHOD__;
    }
}
