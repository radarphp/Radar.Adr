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
            ['radar/adr:adr', 'Radar\Adr\Adr'],
            ['radar/adr:factory', 'Radar\Adr\Factory'],
            ['radar/adr:handlers', 'Radar\Adr\Handlers'],
            ['radar/adr:router', 'Aura\Router\RouterContainer'],
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
            ['Radar\Adr\Handler\ExceptionHandler'],
            ['Radar\Adr\Handler\RoutingHandler'],
            ['Radar\Adr\Handler\SendingHandler'],
            ['Radar\Adr\Handlers'],
            ['Radar\Adr\Router\Map'],
        ];
    }
}
