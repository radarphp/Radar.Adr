<?php
namespace Radar\Adr;

interface DispatcherInterface
{
    public function __invoke(
        array $middle,
        $routingHander,
        $sendingHandler,
        $exceptionHandler
    );
}
