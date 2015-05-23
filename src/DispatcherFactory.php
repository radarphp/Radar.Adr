<?php
namespace Radar\Adr;

class DispatcherFactory
{
    public function __invoke($handlers)
    {
        return new Dispatcher($handlers);
    }
}
