<?php
namespace Radar\Adr\Handler;

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use Radar\Adr\ActionFactory;
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
        $action = $request->getAttribute('radar/adr:action');
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
        $action = $request->getAttribute('radar/adr:action');
        $this->assertSame(
            'Radar\Adr\Responder\RoutingFailedResponder',
            $action->getResponder()
        );

        $expect = $this->matcher->getFailedRoute();
        $actual = call_user_func($action->getDomain());
        $this->assertSame($expect, $actual);

        return $response;
    }
}
