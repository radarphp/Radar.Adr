<?php
namespace Radar\Adr;

use Exception as AnyException;
use Aura\Di\Injection\InjectionFactory;
use Aura\Router\Matcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dispatcher implements DispatcherInterface
{
    protected $factory;
    protected $matcher;
    protected $request;
    protected $response;
    protected $sender;

    public function __construct(
        InjectionFactory $factory,
        ServerRequestInterface $request,
        ResponseInterface $response,
        Sender $sender
    ) {
        $this->factory = $factory;
        $this->request = $request;
        $this->response = $response;
        $this->sender = $sender;
    }

    public function __invoke(
        array $before,
        array $after,
        array $finish,
        $routingHandler,
        $exceptionHandler
    ) {
        try {
            $this->inbound($before, $routingHandler, $after);
        } catch (AnyException $e) {
            $this->handleException($e, $exceptionHandler);
        }

        try {
            $this->outbound($finish);
        } catch (AnyException $e) {
            $this->handleException($e, $exceptionHandler);
        }
    }

    public function inbound($before, $routingHandler, $after)
    {
        $early = $this->middle($before);
        if ($early) {
            return;
        }

        $routingHandler = $this->factory($routingHandler);
        $route = $routingHandler($this->request);
        $this->action($route);
        $this->middle($after);
    }

    protected function outbound($finish)
    {
        $this->sender->__invoke($this->response);
        $this->middle($finish);
    }

    // return true to exit early.
    // use &request &$response to modify the values.
    protected function middle(array $classes)
    {
        // need logging here to say when something runs or not
        foreach ($classes as $class) {
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
        $route->domain
            ? $responder($this->request, $this->response, $this->payload($route->input, $route->domain))
            : $responder($this->request, $this->response);
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
