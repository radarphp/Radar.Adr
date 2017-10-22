<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

/**
 *
 * DI container configuration for Radar classes.
 *
 * @package radar/adr
 *
 */
class Config extends ContainerConfig
{

    /**
     * ADR service name
     */
    const ADR = 'radar/adr:adr';

    /**
     * Resolver service name
     */
    const RESOLVER = 'radar/adr:resolver';

    /**
     * Router service name
     */
    const ROUTER = 'radar/adr:router';

    /**
     *
     * Defines params, setters, values, etc. in the Container.
     *
     * @param Container $di The DI container.
     *
     */
    public function define(Container $di)
    {
        /**
         * Services
         */
        $di->set(self::ADR, $di->lazyNew('Radar\Adr\Adr'));
        $di->set(self::RESOLVER, $di->lazyNew('Radar\Adr\Resolver'));
        $di->set(self::ROUTER, $di->lazyNew('Aura\Router\RouterContainer'));

        /**
         * Aura\Router\Container
         */
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('Radar\Adr\Route');

        /**
         * Relay\RelayBuilder
         */
        $di->params['Relay\RelayBuilder']['resolver'] = $di->lazyGet(self::RESOLVER);

        /**
         * Radar\Adr\Adr
         */
        $di->params['Radar\Adr\Adr']['map'] = $di->lazyGetCall(self::ROUTER, 'getMap');
        $di->params['Radar\Adr\Adr']['rules'] = $di->lazyGetCall(self::ROUTER, 'getRuleIterator');
        $di->params['Radar\Adr\Adr']['relayBuilder'] = $di->lazyNew('Relay\RelayBuilder');

        /**
         * Radar\Adr\Handler\ActionHandler
         */
        $di->params['Radar\Adr\Handler\ActionHandler']['resolver'] = $di->lazyGet(self::RESOLVER);

        /**
         * Radar\Adr\Handler\RoutingHandler
         */
        $di->params['Radar\Adr\Handler\RoutingHandler']['matcher'] = $di->lazyGetCall(self::ROUTER, 'getMatcher');
        $di->params['Radar\Adr\Handler\RoutingHandler']['actionFactory'] = $di->lazyNew('Arbiter\ActionFactory');

        /**
         * Radar\Adr\Resolver
         */
        $di->params['Radar\Adr\Resolver']['injectionFactory'] = $di->getInjectionFactory();
    }

    /**
     *
     * Modifies constructed container objects.
     *
     * @param Container $di The DI container.
     *
     */
    public function modify(Container $di)
    {
    }
}
