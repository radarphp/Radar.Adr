<?php
namespace Radar\Adr;

use Aura\Di\AbstractContainerConfigTest;

class ConfigTest extends AbstractContainerConfigTest
{
    protected function getConfigClasses()
    {
        return [
            'Radar\Adr\Config',
        ];
    }

    public function provideGet()
    {
        return [
            ['radar/adr:adr', 'Radar\Adr\Adr'],
            ['radar/adr:resolver', 'Radar\Adr\Resolver'],
            ['radar/adr:router', 'Aura\Router\RouterContainer'],
        ];
    }

    public function provideNewInstance()
    {
        return [
            ['Aura\Router\RouterContainer'],
            ['Radar\Adr\Adr'],
            ['Radar\Adr\Handler\ActionHandler'],
            ['Radar\Adr\Handler\ExceptionHandler'],
            ['Radar\Adr\Handler\RoutingHandler'],
            ['Radar\Adr\Handler\SendingHandler'],
            ['Radar\Adr\Resolver'],
            ['Radar\Adr\Router\Map'],
        ];
    }
}
