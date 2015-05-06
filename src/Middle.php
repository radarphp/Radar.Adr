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

    public function __invoke(
        ServerRequestInterface &$request,
        ResponseInterface &$response,
        $key
    ) {
        $factory = $this->factory;

        foreach ($this->$key as $class) {
            $object = $factory($class);
            $early = $object($request, $response);
            if ($early instanceof ResponseInterface) {
                $response = $early;
                return $early;
            }
        }
        return false;
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

}