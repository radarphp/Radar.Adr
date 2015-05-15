<?php
namespace Radar\Adr;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Middle
{
    protected $before = [];
    protected $after = [];
    protected $finish = [];

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function run(
        ServerRequestInterface &$request,
        ResponseInterface &$response,
        $key
    ) {
        ksort($this->$key);
        foreach ($this->$key as $priority => $classes) {
            foreach ($classes as $class) {
                $object = $this->factory->invokable($class);
                $early = $object($request, $response);
                if ($early instanceof ResponseInterface) {
                    $response = $early;
                    return $early;
                }
            }
        }
        return false;
    }

    public function before($class, $priority = 0)
    {
        $this->before[$priority][] = $class;
    }

    public function after($class, $priority = 0)
    {
        $this->after[$priority][] = $class;
    }

    public function finish($class, $priority = 0)
    {
        $this->finish[$priority][] = $class;
    }
}
