<?php
namespace Radar\Adr;

class FakeDispatcher implements DispatcherInterface
{
    public function __invoke(
        array $before,
        array $after,
        array $finish,
        $routingHandler,
        $sendingHandler,
        $exceptionHandler
    ) {
        return __METHOD__;
    }
}
