<?php
namespace Radar\Adr;

interface DispatcherInterface
{
    public function __invoke(
        array $before,
        array $after,
        array $finish,
        $routingHander,
        $sendingHandler,
        $exceptionHandler
    );
}
