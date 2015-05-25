<?php
namespace Radar\Adr;

class Handlers
{
    protected $middle = [];

    protected $exceptionHandler = 'Radar\Adr\Handler\ExceptionHandler';

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
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

        return $this->resolver->resolve(array_shift($this->middle));
    }

    public function setExceptionHandler($exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    public function getExceptionHandler()
    {
        return $this->resolver->resolve($this->exceptionHandler);
    }
}
