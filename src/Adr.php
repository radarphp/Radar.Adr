<?php
namespace Radar\Adr;

use Aura\Router\Map;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// a proxy for the map, middle, and dispatcher
class Adr
{
    protected $map;
    protected $middle;
    protected $dispatcher;

    public function __construct(
        Map $map,
        Dispatcher $dispatcher
    ) {
        $this->map = $map;
        $this->dispatcher = $dispatcher;
        $this->middle = $this->dispatcher->middle;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    public function __invoke()
    {
        return call_user_func($this->dispatcher);
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
        $this->dispatcher->exceptionHandler($class);
    }

    public function routingHandler($class)
    {
        $this->dispatcher->routingHandler($class);
    }

    public function sendingHandler($class)
    {
        $this->dispatcher->sendingHandler($class);
    }
}
