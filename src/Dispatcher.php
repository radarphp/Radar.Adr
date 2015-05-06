<?php
namespace Radar\Adr;

use Exception as AnyException;
use Aura\Di\Injection\InjectionFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dispatcher implements DispatcherInterface
{
    protected $factory;
    protected $request;
    protected $response;

    public function __construct(
        InjectionFactory $factory,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->factory = $factory;
        $this->request = $request;
        $this->response = $response;
    }

    public function __invoke(
        array $middle,
        $routingHandler,
        $sendingHandler,
        $exceptionHandler
    ) {
        try {
            $this->inbound($middle, $routingHandler);
        } catch (AnyException $e) {
            $this->handleException($e, $exceptionHandler);
        }

        try {
            $this->outbound($middle, $sendingHandler);
        } catch (AnyException $e) {
            $this->handleException($e, $exceptionHandler);
        }
    }

    public function inbound($middle, $routingHandler)
    {
        $early = $this->middle($middle, 'before');
        if ($early) {
            return;
        }

        $routingHandler = $this->factory($routingHandler);
        $route = $routingHandler($this->request);
        $this->response = $this->action($route);
        $this->middle($middle, 'after');
    }

    protected function outbound($middle, $sendingHandler)
    {
        $sendingHandler = $this->factory($sendingHandler);
        $sendingHandler($this->response);
        $this->middle($middle, 'finish');
    }

    protected function middle(array $middle, $key)
    {
        foreach ($middle[$key] as $class) {
            $object = $this->factory($class);
            $early = $object($this->request, $this->response);
            if ($early instanceof ResponseInterface) {
                $this->response = $early;
                return true;
            }
        }
        return false;
    }

    protected function action($route)
    {
        foreach ($route->attributes as $key => $val) {
            $this->request = $this->request->withAttribute($key, $val);
        }

        $responder = $this->factory($route->responder);

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
        if ($input) {
            $input = $this->factory($input);
            $input = (array) $input($this->request);
        } else {
            $input = [];
        }

        $domain = $this->factory($domain);
        return call_user_func_array($domain, $input);
    }

    protected function handleException($exception, $handler)
    {
        $handler = $this->factory($handler);
        $this->response = $handler($this->request, $this->response, $exception);
    }

    protected function factory($spec)
    {
        if (is_string($spec)) {
            return $this->factory->newInstance($spec);
        }

        if (is_array($spec) && is_string($spec[0])) {
            $spec[0] = $this->factory->newInstance($spec[0]);
            return $spec;
        }

        return $spec;
    }
}
