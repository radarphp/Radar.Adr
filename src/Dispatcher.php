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
        Middle $middle,
        $routingHandler,
        $sendingHandler,
        $exceptionHandler
    ) {
        try {
            $this->inbound($middle, $routingHandler);
        } catch (AnyException $e) {
            $this->exception($e, $exceptionHandler);
        }

        try {
            $this->outbound($middle, $sendingHandler);
        } catch (AnyException $e) {
            $this->exception($e, $exceptionHandler);
        }
    }

    public function inbound(Middle $middle, $routingHandler)
    {
        $early = $middle($this->request, $this->response, 'before');
        if ($early) {
            return;
        }

        $routingHandler = $this->factory($routingHandler);
        $route = $routingHandler($this->request);
        $this->response = $this->action($route);
        $middle($this->request, $this->response, 'after');
    }

    protected function outbound(Middle $middle, $sendingHandler)
    {
        $sendingHandler = $this->factory($sendingHandler);
        $sendingHandler($this->response);
        $middle($this->request, $this->response, 'after');
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

    protected function exception(AnyException $exception, $exceptionHandler)
    {
        $exceptionHandler = $this->factory($exceptionHandler);
        $this->response = $exceptionHandler(
            $this->request,
            $this->response,
            $exception
        );
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
