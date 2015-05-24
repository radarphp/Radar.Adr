<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Radar\Adr\Fake\FakeWare;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $handlers;
    protected $dispatcher;

    protected function setUp()
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance();
        $this->handlers = new Handlers(
            new Resolver($di->getInjectionFactory())
        );

        $this->handlers->setExceptionHandler(
            function ($request, $response, $exception)
            {
                $response = $response->withStatus(500);
                $response->getBody()->write($exception->getMessage());
                return $response;
            }
        );

        $dispatcherFactory = new DispatcherFactory();
        $this->dispatcher = $dispatcherFactory($this->handlers);
    }

    protected function assertResponse($expectStatus, $expectHeaders, $expectBody)
    {
        FakeWare::$count = 0;
        $response = $this->dispatcher->__invoke(
            ServerRequestFactory::fromGlobals(),
            new Response()
        );

        $this->assertEquals($expectBody, $response->getBody()->__toString());
        $this->assertEquals($expectHeaders, $response->getHeaders());
    }

    public function testMiddle()
    {
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWare');
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWare');
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWare');

        $this->assertResponse(
            200,
            [],
            '123'
        );
    }

    public function testError()
    {
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWare');
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWare');
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWareError');
        $this->handlers->appendMiddle('Radar\Adr\Fake\FakeWare');

        $this->assertResponse(
            500,
            [],
            '12Error in middle'
        );
    }
}
