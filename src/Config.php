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
        $di->set('radar/adr:adr', $di->lazyNew('Radar\Adr\Adr'));
        $di->set('radar/adr:resolver', $di->newResolutionHelper());
        $di->set('radar/adr:router', $di->lazyNew('Aura\Router\RouterContainer'));

        /**
         * Aura\Router\Container
         */
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('Radar\Adr\Route');

        /**
         * Relay\RelayBuilder
         */
        $di->params['Relay\RelayBuilder']['resolver'] = $di->lazyGet('radar/adr:resolver');

        /**
         * Radar\Adr\Adr
         */
        $di->params['Radar\Adr\Adr']['map'] = $di->lazyGetCall('radar/adr:router', 'getMap');
        $di->params['Radar\Adr\Adr']['rules'] = $di->lazyGetCall('radar/adr:router', 'getRuleIterator');
        $di->params['Radar\Adr\Adr']['relayBuilder'] = $di->lazyNew('Relay\RelayBuilder');

        /**
         * Radar\Adr\Handler\ActionHandler
         */
        $di->params['Radar\Adr\Handler\ActionHandler']['resolver'] = $di->lazyGet('radar/adr:resolver');

        /**
         * Radar\Adr\Handler\RoutingHandler
         */
        $di->params['Radar\Adr\Handler\RoutingHandler']['matcher'] = $di->lazyGetCall('radar/adr:router', 'getMatcher');
        $di->params['Radar\Adr\Handler\RoutingHandler']['actionFactory'] = $di->lazyNew('Arbiter\ActionFactory');

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
