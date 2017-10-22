<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Arbiter\ActionFactory;
use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Router\RouterContainer as Router;
use Relay\RelayBuilder;

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
        $di->set(self::ADR, $di->lazyNew(Adr::class));
        $di->set(self::RESOLVER, $di->lazyNew(Resolver::class));
        $di->set(self::ROUTER, $di->lazyNew(Router::class));

        /**
         * Aura\Router\Container
         */
        $di->setters[Router::class]['setRouteFactory'] = $di->newFactory(Route::class);

        /**
         * Relay\RelayBuilder
         */
        $di->params[RelayBuilder::class]['resolver'] = $di->lazyGet(self::RESOLVER);

        /**
         * Radar\Adr\Adr
         */
        $di->params[Adr::class]['map'] = $di->lazyGetCall(self::ROUTER, 'getMap');
        $di->params[Adr::class]['rules'] = $di->lazyGetCall(self::ROUTER, 'getRuleIterator');
        $di->params[Adr::class]['relayBuilder'] = $di->lazyNew(RelayBuilder::class);

        /**
         * Radar\Adr\Handler\ActionHandler
         */
        $di->params[Handler\ActionHandler::class]['resolver'] = $di->lazyGet(self::RESOLVER);

        /**
         * Radar\Adr\Handler\RoutingHandler
         */
        $di->params[Handler\RoutingHandler::class]['matcher'] = $di->lazyGetCall(self::ROUTER, 'getMatcher');
        $di->params[Handler\RoutingHandler::class]['actionFactory'] = $di->lazyNew(ActionFactory::class);

        /**
         * Radar\Adr\Resolver
         */
        $di->params[Resolver::class]['injectionFactory'] = $di->getInjectionFactory();
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
