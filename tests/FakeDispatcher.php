<?php
namespace Radar\Adr;

class FakeDispatcher implements DispatcherInterface
{
    protected $map;

    public function __construct(FakeMap $map)
    {
        $this->map = $map;
    }

    public function __invoke(
        array $before,
        array $after,
        array $finish,
        array $error
    ) {
        return __METHOD__;
    }

    public function map()
    {
        return $this->map;
    }
}
