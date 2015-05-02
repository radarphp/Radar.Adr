<?php
namespace Radar\Adr;

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
        Matcher $matcher,
        ServerRequestInterface $request,
        ResponseInterface $response,
        Sender $sender
    ) {
        $this->factory = $factory;
        $this->matcher = $matcher;
        $this->request = $request;
        $this->response = $response;
        $this->sender = $sender;
    }

    public function __invoke(
        array $before,
        array $after,
        array $finish,
        array $error
    ) {
        try {
            $this->startup($before);
        } catch (Exception $e) {
            $this->error($e, $error);
        }

        try {
            $this->shutdown($after, $finish);
        } catch (Exception $e) {
            $this->error($e, $error);
        }
    }

    public function map()
    {
        return $this->matcher->getMap();
    }

    protected function startup($before)
    {
        $early = $this->middle($before);
        if (! $early) {
            $route = $this->route();
            $this->action($route->input, $route->domain, $route->responder);
        }
    }

    protected function shutdown($after, $finish)
    {
        $this->middle($after);
        $this->sender->send($this->response);
        $this->middle($finish);
    }

    protected function middle(array $classes)
    {
        // need logging here to say when something runs or not
        foreach ($classes as $class) {
            $object = $this->factory->newInstance($class);
            $early = $object($this->request, $this->response);
            if ($early instanceof ResponseInterface) {
                $this->response = $early;
                return true;
            }
        }
        return false;
    }

    protected function route()
    {
        $route = $this->matcher->match($this->request);
        if ($route) {
            $this->attributes($route->attributes);
            return $route;
        }

        $e = new Exception\RoutingFailed();
        $e->setFailedRoute($this->matcher->getFailedRoute());
        throw $e;
    }

    protected function action($input, $domain, $responder)
    {
        $payload = $domain
            ? $this->payload($input, $domain)
            : null;

        $responder = $this->factory->newInstance($responder);

        $this->response = $payload
            ? $responder($this->request, $this->response, $payload)
            : $responder($this->request, $this->response);
    }

    protected function payload($input, $domain)
    {
        $input = $this->factory->newInstance($input);
        $input = $input($this->request);
        $domain = $this->domain($domain);
        return call_user_func_array($domain, (array) $input);
    }

    protected function domain($domain)
    {
        if (is_string($domain)) {
            return $this->factory->newInstance($domain);
        }

        if (is_array($domain) && is_string($domain[0])) {
            $domain[0] = $this->factory->newInstance($domain[0]);
            return $domain;
        }

        return $domain;
    }

    protected function error($e, array $error)
    {
        $this->attributes(['radar/adr:exception' => $e]);
        call_user_func_array([$this, 'action'], $error);
    }

    protected function attributes(array $attributes)
    {
        foreach ($attributes as $key => $val) {
            $this->request = $this->request->withAttribute($key, $val);
        }
    }
}
