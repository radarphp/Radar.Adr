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
            $early = $this->middle($before);
            if (! $early) {
                $route = $this->route();
                $this->action($route->input, $route->domain, $route->responder);
            }
        } catch (AnyException $e) {
            $this->error($e, $error);
        }

        try {
            $this->middle($after);
        } catch (AnyException $e) {
            $this->error($e, $error);
        }

        try {
            $this->sender->__invoke($this->response);
            $this->middle($finish);
        } catch (AnyException $e) {
            $this->error($e, $error);
        }
    }

    public function map()
    {
        return $this->matcher->getMap();
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
        $responder = $this->factory->newInstance($responder);
        $this->response = $domain
            ? $responder($this->request, $this->response, $this->payload($input, $domain))
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

    protected function error(AnyException $e, array $error)
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
