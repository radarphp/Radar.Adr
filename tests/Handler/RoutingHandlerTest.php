<?php
namespace Radar\Adr\Handler;

use Arbiter\ActionFactory;
use Aura\Router\Map;
use Aura\Router\RouterContainer;
use Radar\Adr\Route;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class RoutingHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $map;
    protected $matcher;
    protected $routingHandler;

    protected function setUp()
    {
        $actionr = new RouterContainer();
        $actionr->setMapFactory(function () { return new Map(new Route()); } );

        $this->map = $actionr->getMap();
        $this->matcher = $actionr->getMatcher();
        $this->routingHandler = new RoutingHandler($this->matcher, new ActionFactory());
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
        $this->routingHandler->__invoke(
            $request,
            [$this, 'assertFound']
        );
    }

    public function assertFound($request)
    {
        $action = $request->getAttribute('radar/adr:action');
        $id = $request->getAttribute('id');
        $this->assertSame('88', $id);
    }

    public function testNotFound()
    {
        $this->map->get('Radar\Adr\Fake\Action', '/fake/{id}', 'FakeDomain');
        $request = $this->newRequest('/wrong/path');
        $returnedResponse = $this->routingHandler->__invoke(
            $request,
            [$this, 'assertNotFound']
        );
    }

    public function assertNotFound($request)
    {
        $action = $request->getAttribute('radar/adr:action');
        $this->assertSame(
            'Radar\Adr\Responder\RoutingFailedResponder',
            $action->getResponder()
        );

        $expect = $this->matcher->getFailedRoute();
        $actual = call_user_func($action->getDomain());
        $this->assertSame($expect, $actual);
    }

    public function testCustomNotFound()
    {
        $routingHandler = new RoutingHandler(
            $this->matcher,
            new ActionFactory(),
            'Radar\Adr\Fake\FakeRoutingFailedResponder'
        );

        $this->map->get('Radar\Adr\Fake\Action', '/fake/{id}', 'FakeDomain');
        $request = $this->newRequest('/wrong/path');
        $returnedResponse = $routingHandler->__invoke(
            $request,
            [$this, 'assertCustomNotFound']
        );
    }

    public function assertCustomNotFound($request)
    {
        $action = $request->getAttribute('radar/adr:action');
        $this->assertSame(
            'Radar\Adr\Fake\FakeRoutingFailedResponder',
            $action->getResponder()
        );

        $expect = $this->matcher->getFailedRoute();
        $actual = call_user_func($action->getDomain());
        $this->assertSame($expect, $actual);
    }
}
