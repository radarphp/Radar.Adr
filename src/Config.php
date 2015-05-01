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
         * Radar\Adr\Adr
         */
        $di->params['Radar\Adr\Adr']['factory'] = $di->getInjectionFactory();
        $di->params['Radar\Adr\Adr']['matcher'] = $di->lazyGetCall('radar/adr:router', 'getMatcher');
        $di->params['Radar\Adr\Adr']['request'] = $di->lazy(['Phly\Http\ServerRequestFactory', 'fromGlobals']);
        $di->params['Radar\Adr\Adr']['response'] = $di->lazyNew('Phly\Http\Response');
        $di->params['Radar\Adr\Adr']['sender'] = $di->lazyNew('Radar\Adr\Sender');

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
