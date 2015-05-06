<?php
namespace Radar\Adr;

use Aura\Di\Injection\InjectionFactory;
use Aura\Router\Map;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Adr
{
    protected $map;
    protected $middle;
    protected $dispatcher;
    protected $routingHandler = 'Radar\Adr\RoutingHandler';
    protected $sendingHandler = 'Radar\Adr\SendingHandler';
    protected $exceptionHandler = 'Radar\Adr\ExceptionHandler';

    public function __construct(
        Map $map,
        Middle $middle,
        Dispatcher $dispatcher
    ) {
        $this->map = $map;
        $this->middle = $middle;
        $this->dispatcher = $dispatcher;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    public function __invoke()
    {
        return call_user_func_array($this->dispatcher, $this->getDispatcherParams());
    }

    public function before($class)
    {
        $this->middle->before($class);
    }

    public function after($class)
    {
        $this->middle->after($class);
    }

    public function finish($class)
    {
        $this->middle->finish($class);
    }

    public function exceptionHandler($class)
    {
        $this->exceptionHandler = $class;
    }

    public function routingHandler($class)
    {
        $this->routingHandler = $class;
    }

    public function sendingHandler($class)
    {
        $this->sendingHandler = $class;
    }

    public function getDispatcherParams()
    {
        return [
            'middle' => $this->middle,
            'routingHandler' => $this->routingHandler,
            'sendingHandler' => $this->sendingHandler,
            'exceptionHandler' => $this->exceptionHandler,
        ];
    }
}
