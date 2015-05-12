<?php
namespace Radar\Adr;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Router\Route;

class Dispatcher
{
    protected $factory;
    protected $request;
    protected $response;
    protected $middle;
    protected $routingHandler = 'Radar\Adr\Handler\RoutingHandler';
    protected $sendingHandler = 'Radar\Adr\Handler\SendingHandler';
    protected $exceptionHandler = 'Radar\Adr\Handler\ExceptionHandler';
    protected $actionHandler = 'Radar\Adr\Handler\ActionHandler';

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
        } catch (Exception $e) {
            $this->exception($e);
        }

        try {
            $this->outbound();
        } catch (Exception $e) {
            $this->exception($e);
        }
    }

    public function actionHandler($spec)
    {
        $this->actionHandler = $spec;
    }

    public function exceptionHandler($spec)
    {
        $this->exceptionHandler = $spec;
    }

    public function routingHandler($spec)
    {
        $this->routingHandler = $spec;
    }

    public function sendingHandler($spec)
    {
        $this->sendingHandler = $spec;
    }

    protected function inbound()
    {
        $middle = $this->middle;

        $early = $middle($this->request, $this->response, 'before');
        if ($early) {
            return;
        }

        $this->response = $this->action($this->route());
        $middle($this->request, $this->response, 'after');
    }

    protected function route()
    {
        $factory = $this->factory;

        $routingHandler = $factory($this->routingHandler);
        $route = $routingHandler($this->request);
        foreach ($route->attributes as $key => $val) {
            $this->request = $this->request->withAttribute($key, $val);
        }
        return $route;
    }

    protected function action(Route $route)
    {
        $factory = $this->factory;
        $actionHandler = $factory($this->actionHandler);
        return $actionHandler($this->request, $this->response, $route);
    }

    protected function outbound()
    {
        $factory = $this->factory;
        $sendingHandler = $factory($this->sendingHandler);
        $sendingHandler($this->response);

        $middle = $this->middle;
        $middle($this->request, $this->response, 'finish');
    }


    protected function exception(Exception $exception)
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
