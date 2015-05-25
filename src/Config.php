<?php
namespace Radar\Adr;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class Config extends ContainerConfig
{
    public function define(Container $di)
    {
        /**
         * Services
         */
        $di->set('radar/adr:adr', $di->lazyNew('Radar\Adr\Adr'));
        $di->set('radar/adr:resolver', $di->lazyNew('Radar\Adr\Resolver'));
        $di->set('radar/adr:router', $di->lazyNew('Aura\Router\RouterContainer'));

        /**
         * Aura\Router\Container
         */
        $di->setters['Aura\Router\RouterContainer']['setMapFactory'] = $di->newFactory('Radar\Adr\Router\Map');

        /**
         * Radar\Adr\Adr
         */
        $di->params['Radar\Adr\Adr']['map'] = $di->lazyGetCall('radar/adr:router', 'getMap');
        $di->params['Radar\Adr\Adr']['rules'] = $di->lazyGetCall('radar/adr:router', 'getRuleIterator');
        $di->params['Radar\Adr\Adr']['handlers'] = $di->lazyNew('Radar\Adr\Handlers');
        $di->params['Radar\Adr\Adr']['dispatcherFactory'] = $di->lazyNew('Radar\Adr\DispatcherFactory');

        /**
         * Radar\Adr\Handler\ActionHandler
         */
        $di->params['Radar\Adr\Handler\ActionHandler']['resolver'] = $di->lazyGet('radar/adr:resolver');

        /**
         * Radar\Adr\Handler\ExceptionHandler
         */
        $di->params['Radar\Adr\Handler\ExceptionHandler']['sender'] = $di->lazyNew('Radar\Adr\Sender');

        /**
         * Radar\Adr\Handler\RoutingHandler
         */
        $di->params['Radar\Adr\Handler\RoutingHandler']['matcher'] = $di->lazyGetCall('radar/adr:router', 'getMatcher');
        $di->params['Radar\Adr\Handler\RoutingHandler']['route'] = $di->lazyNew('Radar\Adr\Router\Route');

        /**
         * Radar\Adr\Handler\SendingHandler
         */
        $di->params['Radar\Adr\Handler\SendingHandler']['sender'] = $di->lazyNew('Radar\Adr\Sender');

        /**
         * Radar\Adr\Handlers
         */
        $di->params['Radar\Adr\Handlers']['resolver'] = $di->lazyGet('radar/adr:resolver');

        /**
         * Radar\Adr\Resolver
         */
        $di->params['Radar\Adr\Resolver']['injectionFactory'] = $di->getInjectionFactory();

        /**
         * Radar\Adr\Router\Map
         */
        $di->params['Radar\Adr\Router\Map']['protoRoute'] = $di->lazyNew('Radar\Adr\Router\Route');
    }

    public function modify(Container $di)
    {
    }
}
