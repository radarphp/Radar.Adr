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
        $di->params['Radar\Adr\Dispatcher']['factory'] = $di->getInjectionFactory();
        $di->params['Radar\Adr\Dispatcher']['request'] = $di->lazy(['Phly\Http\ServerRequestFactory', 'fromGlobals']);
        $di->params['Radar\Adr\Dispatcher']['response'] = $di->lazyNew('Phly\Http\Response');
        $di->params['Radar\Adr\Dispatcher']['sender'] = $di->lazyNew('Radar\Adr\Sender');

        /**
         * Radar\Adr\Map
         */
        $di->params['Radar\Adr\Map']['protoRoute'] = $di->lazyNew('Radar\Adr\Route');

        /**
         * Radar\Adr\RoutingHandler
         */
        $di->params['Radar\Adr\RoutingHandler']['matcher'] = $di->lazyGetCall('radar/adr:router', 'getMatcher');
    }

    public function modify(Container $di)
    {
    }
}
