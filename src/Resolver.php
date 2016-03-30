<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Aura\Di\Injection\InjectionFactory;

/**
 *
 * Resolves object specifications using the DI container.
 *
 * @package radar/adr
 *
 */
class Resolver
{
    protected $injectionFactory;

    public function __construct(InjectionFactory $injectionFactory)
    {
        $this->injectionFactory = $injectionFactory;
    }

    public function __invoke($spec)
    {
        if (is_string($spec)) {
            return $this->injectionFactory->newInstance($spec);
        }

        if (is_array($spec) && is_string($spec[0])) {
            $spec[0] = $this->injectionFactory->newInstance($spec[0]);
        }

        return $spec;
    }
}
