<?php
namespace Radar\Adr\Handler;

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Radar\Adr\Route;

class RoutingHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $map;
    protected $matcher;
    protected $routingHandler;

    protected function setUp()
    {
        $router = new RouterContainer();
        $router->setMapFactory(function () { return new Map(new Route()); } );

        $this->map = $router->getMap();
        $this->matcher = $router->getMatcher();
        $this->routingHandler = new RoutingHandler($this->matcher, new Route());
    }

    protected function newRequest($path)
    {
        $_SERVER['REQUEST_URI'] = $path;
        return ServerRequestFactory::fromGlobals();
    }

    public function testFound()
    {
        $this->map->get('Radar\Adr\Fake\Action', '/fake/{id}', 'FakeDomain');
        $request = $this->newRequest('/fake/88');
        $response = new Response();
        $returnedResponse = $this->routingHandler->__invoke(
            $request,
            $response,
            [$this, 'assertFound']
        );
        $this->assertSame($response, $returnedResponse);
    }

    public function assertFound($request, $response)
    {
        $route = $request->getAttribute('radar/adr:route');
        $this->assertSame('/fake/{id}', $route->path);

        $id = $request->getAttribute('id');
        $this->assertSame('88', $id);

        return $response;
    }

    public function testNotFound()
    {
        $this->map->get('Radar\Adr\Fake\Action', '/fake/{id}', 'FakeDomain');
        $request = $this->newRequest('/wrong/path');
        $response = new Response();
        $returnedResponse = $this->routingHandler->__invoke(
            $request,
            $response,
            [$this, 'assertNotFound']
        );
        $this->assertSame($response, $returnedResponse);
    }

    public function assertNotFound($request, $response)
    {
        $route = $request->getAttribute('radar/adr:route');
        $this->assertSame('Radar\Adr\Responder\RoutingFailedResponder', $route->responder);

        $expect = $this->matcher->getFailedRoute();
        $actual = call_user_func($route->domain);
        $this->assertSame($expect, $actual);

        return $response;
    }
}
