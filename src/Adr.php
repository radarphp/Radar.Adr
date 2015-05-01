<?php
namespace Radar\Adr;

use Aura\Di\Injection\InjectionFactory;
use Aura\Router\Matcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Adr
{
    protected $factory;
    protected $matcher;
    protected $request;
    protected $response;
    protected $sender;

    protected $map;
    protected $error;
    protected $before = [];
    protected $after = [];
    protected $finish = [];

    // inject a logger too
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

        $this->map = $this->matcher->getMap();
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
        try {
            $this->run();
        } catch (Exception $e) {
            $this->runError($e);
        }

        try {
            $this->sender->send($this->response);
            $this->runMiddle($this->finish);
        } catch (Exception $e) {
            $this->runError($e);
        }
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

    protected function run()
    {
        $early = $this->runMiddle($this->before);
        if (! $early) {
            $route = $this->runRoute();
            $this->runAction($route->input, $route->domain, $route->responder);
        }
        $this->runMiddle($this->after);
    }

    protected function runMiddle(array $classes)
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

    protected function runRoute()
    {
        $route = $this->matcher->match($this->request);
        if ($route) {
            $this->addRequestAttributes($route->attributes);
            return $route;
        }

        $e = new Exception\RoutingFailed();
        $e->setFailedRoute($this->matcher->getFailedRoute());
        throw $e;
    }

    protected function runAction($input, $domain, $responder)
    {
        $payload = null;
        if ($domain) {
            $input = $this->factory->newInstance($input);
            $input = (array) $input($this->request);
            $domain = $this->factory->newInstance($domain);
            $payload = call_user_func_array($domain, $input);
        }

        $responder = $this->factory->newInstance($responder);
        $this->response = $responder($this->request, $this->response, $payload);
    }

    protected function runError($e)
    {
        $this->addRequestAttributes(['radar/adr:exception' => $e]);
        $this->runAction(
            $this->error->input,
            $this->error->domain,
            $this->error->responder
        );
    }

    protected function addRequestAttributes(array $attributes)
    {
        foreach ($attributes as $key => $val) {
            $this->request = $this->request->withAttribute($key, $val);
        }
    }
}
