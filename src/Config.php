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
        $di->set('radar/adr:router', $di->lazyNew('Aura\Router\RouterContainer'));

        /**
         * Aura\Router\Container
         */
        $di->setters['Aura\Router\RouterContainer']['setMapFactory'] = $di->newFactory('Radar\Adr\Map');

        /**
         * Radar\Adr\Adr
         */
        $di->params['Radar\Adr\Adr']['map'] = $di->lazyGetCall('radar/adr:router', 'getMap');
        $di->params['Radar\Adr\Adr']['dispatcher'] = $di->lazyNew('Radar\Adr\Dispatcher');

        /**
         * Radar\Adr\Dispatcher
         */
        $di->params['Radar\Adr\Dispatcher']['factory'] = $di->lazyNew('Radar\Adr\Factory');
        $di->params['Radar\Adr\Dispatcher']['request'] = $di->lazy(['Phly\Http\ServerRequestFactory', 'fromGlobals']);
        $di->params['Radar\Adr\Dispatcher']['response'] = $di->lazyNew('Phly\Http\Response');
        $di->params['Radar\Adr\Dispatcher']['middle'] = $di->lazyNew('Radar\Adr\Middle');

        /**
         * Radar\Adr\Factory
         */
        $di->params['Radar\Adr\Factory']['injectionFactory'] = $di->getInjectionFactory();

        /**
         * Radar\Adr\Middle
         */
        $di->params['Radar\Adr\Middle']['factory'] = $di->lazyNew('Radar\Adr\Factory');

        /**
         * Radar\Adr\Map
         */
        $di->params['Radar\Adr\Map']['protoRoute'] = $di->lazyNew('Radar\Adr\Route');

        /**
         * Radar\Adr\RoutingHandler
         */
        $di->params['Radar\Adr\RoutingHandler']['matcher'] = $di->lazyGetCall('radar/adr:router', 'getMatcher');
        $di->params['Radar\Adr\RoutingHandler']['route'] = $di->lazyNew('Radar\Adr\Route');
    }

    public function modify(Container $di)
    {
    }
}
