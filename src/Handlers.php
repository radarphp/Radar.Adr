<?php
namespace Radar\Adr;

class Handlers
{
    protected $middle = [];

    protected $exceptionHandler = 'Radar\Adr\Handler\ExceptionHandler';

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function appendMiddle($spec)
    {
        $this->middle[] = $spec;
    }

    public function shiftMiddle()
    {
        if (! $this->middle) {
            return function ($request, $response) { return $response; };
        }

        return $this->factory->invokable(array_shift($this->middle));
    }

    public function setExceptionHandler($exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    public function getExceptionHandler()
    {
        return $this->factory->invokable($this->exceptionHandler);
    }
}
