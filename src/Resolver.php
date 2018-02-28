<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware;

use Invoker\Invoker;
use Psr\Container\ContainerInterface;

/**
 *
 * Resolves object specifications using a given PSR-11 compliant DI container.
 *
 * @package radar/middleware
 *
 */
class Resolver
{
    /**
     * Any PSR-11 compliant container
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * Resolves an object specification.
     *
     * @param mixed $spec The object specification.
     *
     * @return mixed
     *
     * @throws \Invoker\Exception\InvocationException
     * @throws \Invoker\Exception\NotCallableException
     */
    public function __invoke($spec)
    {
        $invoker = new Invoker(null, $this->container);

        if (\is_string($spec)) {
            return $invoker->getCallableResolver()->resolve($spec);
        }

        if (\is_array($spec) && \is_string($spec[0])) {
            $spec[0] = $invoker->getCallableResolver()->resolve($spec[0]);
        }

        return $spec;
    }
}
