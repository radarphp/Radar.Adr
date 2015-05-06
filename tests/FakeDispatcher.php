<?php
namespace Radar\Adr;

class FakeDispatcher implements DispatcherInterface
{
    public function __invoke(
        array $before,
        array $after,
        array $finish,
        $routingHander,
        $exceptionHandler
    ) {
        return __METHOD__;
    }
}
