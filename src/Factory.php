<?php
namespace Radar\Adr;

use Aura\Di\Injection\InjectionFactory;

class Factory
{
    protected $injectionFactory;

    public function __construct(InjectionFactory $injectionFactory)
    {
        $this->injectionFactory = $injectionFactory;
    }

    public function invokable($spec)
    {
        if (is_string($spec)) {
            return $this->injectionFactory->newInstance($spec);
        }

        if (is_array($spec) && is_string($spec[0])) {
            $spec[0] = $this->injectionFactory->newInstance($spec[0]);
            return $spec;
        }

        return $spec;
    }
}
