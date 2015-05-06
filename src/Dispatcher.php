<?php
namespace Radar\Adr;

use Exception as AnyException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dispatcher
{
    protected $factory;
    protected $request;
    protected $response;
    protected $middle;
    protected $routingHandler = 'Radar\Adr\RoutingHandler';
    protected $sendingHandler = 'Radar\Adr\SendingHandler';
    protected $exceptionHandler = 'Radar\Adr\ExceptionHandler';

    public function __construct(
        Factory $factory,
        ServerRequestInterface $request,
        ResponseInterface $response,
        Middle $middle
    ) {
        $this->factory = $factory;
        $this->request = $request;
        $this->response = $response;
        $this->middle = $middle;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public function __invoke()
    {
        try {
            $this->inbound();
        } catch (AnyException $e) {
            $this->exception($e);
        }

        try {
            $this->outbound();
        } catch (AnyException $e) {
            $this->exception($e);
        }
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

    protected function inbound()
    {
        $middle = $this->middle;

        $early = $middle($this->request, $this->response, 'before');
        if ($early) {
            return;
        }

        $factory = $this->factory;
        $routingHandler = $factory($this->routingHandler);
        $route = $routingHandler($this->request);
        $this->response = $this->action($route);

        $middle($this->request, $this->response, 'after');
    }

    protected function outbound()
    {
        $factory = $this->factory;
        $sendingHandler = $factory($this->sendingHandler);
        $sendingHandler($this->response);

        $middle = $this->middle;
        $middle($this->request, $this->response, 'after');
    }

    protected function action($route)
    {
        foreach ($route->attributes as $key => $val) {
            $this->request = $this->request->withAttribute($key, $val);
        }

        $factory = $this->factory;
        $responder = $factory($route->responder);

        if ($route->domain) {
            return $responder(
                $this->request,
                $this->response,
                $this->payload($route->input, $route->domain)
            );
        }

        return $responder($this->request, $this->response);
    }

    protected function payload($input, $domain)
    {
        $factory = $this->factory;

        if ($input) {
            $input = $factory($input);
            $input = (array) $input($this->request);
        } else {
            $input = [];
        }

        $domain = $factory($domain);
        return call_user_func_array($domain, $input);
    }

    protected function exception(AnyException $exception)
    {
        $factory = $this->factory;
        $exceptionHandler = $factory($this->exceptionHandler);
        $this->response = $exceptionHandler(
            $this->request,
            $this->response,
            $exception
        );
    }
}
