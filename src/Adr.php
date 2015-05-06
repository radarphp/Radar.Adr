<?php
namespace Radar\Adr;

use Aura\Di\Injection\InjectionFactory;
use Aura\Router\Map;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Adr
{
    protected $dispatcher;
    protected $map;
    protected $before = [];
    protected $after = [];
    protected $finish = [];
    protected $routingHandler = 'Radar\Adr\RoutingHandler';
    protected $sendingHandler = 'Radar\Adr\SendingHandler';
    protected $exceptionHandler = 'Radar\Adr\ExceptionHandler';

    public function __construct(Map $map, DispatcherInterface $dispatcher)
    {
        $this->map = $map;
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
        $this->before[] = $class;
    }

    public function after($class)
    {
        $this->after[] = $class;
    }

    public function finish($class)
    {
        $this->finish[] = $class;
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
            'before' => $this->before,
            'after' => $this->after,
            'finish' => $this->finish,
            'routingHandler' => $this->routingHandler,
            'sendingHandler' => $this->sendingHandler,
            'exceptionHandler' => $this->exceptionHandler,
        ];
    }
}
