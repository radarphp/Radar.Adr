<?php
namespace Radar\Adr;

use Aura\Di\Injection\InjectionFactory;
use Aura\Router\Matcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Adr
{
    protected $dispatcher;
    protected $map;
    protected $before = [];
    protected $after = [];
    protected $finish = [];
    protected $error;

    // inject a logger too
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->map = $this->dispatcher->map();
        $this->error(
            'Radar\Adr\Input',
            'Radar\Adr\Error\Domain',
            'Radar\Adr\Error\Responder'
        );
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    public function __invoke()
    {
        $this->dispatcher->__invoke(
            $this->before,
            $this->after,
            $this->finish,
            $this->error
        );
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

    public function error($input, $domain, $responder)
    {
        $this->error = (object) [
            'input' => $input,
            'domain' => $domain,
            'responder' => $responder
        ];
    }
}
