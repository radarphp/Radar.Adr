<?php
namespace Radar\Adr;

use Aura\Di\_Config\AbstractContainerTest;

class ConfigTest extends AbstractContainerTest
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
            ['radar/adr:router', 'Aura\Router\RouterContainer'],
            ['radar/adr:adr', 'Radar\Adr\Adr'],
        ];
    }

    public function provideNewInstance()
    {
        return [
            ['Aura\Router\RouterContainer'],
            ['Radar\Adr\Adr'],
            ['Radar\Adr\Dispatcher'],
            ['Radar\Adr\Factory'],
            ['Radar\Adr\Handler\ActionHandler'],
            ['Radar\Adr\Handler\RoutingHandler'],
            ['Radar\Adr\Middle'],
            ['Radar\Adr\Router\Map'],
        ];
    }
}
