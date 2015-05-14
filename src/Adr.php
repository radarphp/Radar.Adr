<?php
namespace Radar\Adr;

use Aura\Router\Rule\RuleIterator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Router\Map;

// a proxy for the map, middle, and dispatcher
class Adr
{
    protected $map;
    protected $middle;
    protected $rules;
    protected $dispatcher;

    public function __construct(
        Map $map,
        RuleIterator $rules,
        Dispatcher $dispatcher
    ) {
        $this->map = $map;
        $this->rules = $rules;
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

    public function before($spec)
    {
        $this->middle->before($spec);
    }

    public function after($spec)
    {
        $this->middle->after($spec);
    }

    public function finish($spec)
    {
        $this->middle->finish($spec);
    }

    public function rules()
    {
        return $this->rules;
    }

    public function actionHandler($spec)
    {
        $this->dispatcher->actionHandler($spec);
    }

    public function exceptionHandler($spec)
    {
        $this->dispatcher->exceptionHandler($spec);
    }

    public function routingHandler($spec)
    {
        $this->dispatcher->routingHandler($spec);
    }

    public function sendingHandler($spec)
    {
        $this->dispatcher->sendingHandler($spec);
    }
}
