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
    protected $dispatcher;

    public function __construct(
        Map $map,
        RuleIterator $rules,
        Handlers $handlers,
        Dispatcher $dispatcher
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->dispatcher = $dispatcher;
        $this->handlers = $handlers;
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
        return $this->dispatcher->__invoke($request, $response);
    }
}
