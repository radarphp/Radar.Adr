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
    protected $error = [
        'Radar\Adr\Input',
        'Radar\Adr\Error\Domain',
        'Radar\Adr\Error\Responder'
    ];

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->map = $this->dispatcher->map();
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    public function __invoke()
    {
        return call_user_func_array($this->dispatcher, $this->getMiddles());
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
        $this->error = func_get_args();
    }

    public function getMiddles()
    {
        return [
            'before' => $this->before,
            'after' => $this->after,
            'finish' => $this->finish,
            'error' => $this->error
        ];
    }
}
