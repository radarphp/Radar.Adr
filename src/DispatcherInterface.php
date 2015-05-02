<?php
namespace Radar\Adr;

interface DispatcherInterface
{
    public function __invoke(
        array $before,
        array $after,
        array $finish,
        array $error
    );

    public function map();
}
