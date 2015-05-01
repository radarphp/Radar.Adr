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
        $di->setters['Aura\Router\RouterContainer']['setMapFactory'] = $di->newFactory('Radar\Adr\Router\Map');

        /**
         * Radar\Adr\Dispatcher
         */
        $di->params['Radar\Adr\Adr']['dispatcher'] = $di->lazyNew('Radar\Adr\Dispatcher');

        /**
         * Radar\Adr\Dispatcher
         */
        $di->params['Radar\Adr\Dispatcher']['factory'] = $di->getInjectionFactory();
        $di->params['Radar\Adr\Dispatcher']['matcher'] = $di->lazyGetCall('radar/adr:router', 'getMatcher');
        $di->params['Radar\Adr\Dispatcher']['request'] = $di->lazy(['Phly\Http\ServerRequestFactory', 'fromGlobals']);
        $di->params['Radar\Adr\Dispatcher']['response'] = $di->lazyNew('Phly\Http\Response');
        $di->params['Radar\Adr\Dispatcher']['sender'] = $di->lazyNew('Radar\Adr\Sender');

        /**
         * Radar\Adr\Error\Domain
         */
        $di->params['Radar\Adr\Error\Domain']['payload'] = $di->lazyNew('Aura\Payload\Payload');

        /**
         * Radar\Adr\Responder
         */
        $di->params['Radar\Adr\Responder']['generator'] = $di->lazyGetCall('radar/adr:router', 'getGenerator');

        /**
         * Radar\Adr\Router\Map
         */
        $di->params['Radar\Adr\Router\Map']['protoRoute'] = $di->lazyNew('Radar\Adr\Router\Route');
    }

    public function modify(Container $di)
    {
    }
}
