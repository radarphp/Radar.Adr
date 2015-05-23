<?php
namespace Radar\Adr;

use Aura\Router\Rule\RuleIterator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Router\Map;

class Adr
{
    protected $map;
    protected $handlers;
    protected $rules;
    protected $dispatcherFactory;

    public function __construct(
        Map $map,
        RuleIterator $rules,
        Handlers $handlers,
        callable $dispatcherFactory
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->handlers = $handlers;
        $this->dispatcherFactory = $dispatcherFactory;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    public function rules()
    {
        return $this->rules;
    }

    public function middle($spec)
    {
        return $this->handlers->appendMiddle($spec);
    }

    public function exceptionHandler($spec)
    {
        return $this->handlers->setExceptionHandler($spec);
    }

    public function run(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $dispatcher = call_user_func($this->dispatcherFactory, $this->handlers);
        return $dispatcher($request, $response);
    }
}
