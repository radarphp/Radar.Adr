<?php
namespace Radar\Adr\Handler;

use Arbiter\Action;
use Aura\Di\ContainerBuilder;
use Radar\Adr\Route;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class ActionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $actionHandler;

    protected function setUp()
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance();
        $this->actionHandler = new ActionHandler(
            $di->newResolutionHelper()
        );
    }

    protected function assertResponse(Action $action, $expectStatus, $expectHeaders, $expectBody)
    {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute('radar/adr:action', $action);
        $response = $this->actionHandler->__invoke(
            $request,
            new Response(),
            function ($request, $response) { return $response; }
        );
        $this->assertEquals($expectStatus, $response->getStatusCode());
        $this->assertEquals($expectBody, $response->getBody()->__toString());
        $this->assertEquals($expectHeaders, $response->getHeaders());
    }

    public function testDomainClass()
    {
        $action = new Action(
            null,
            'Radar\Adr\Fake\FakeDomain',
            'Radar\Adr\Responder\Responder'
        );

        $this->assertResponse(
            $action,
            200,
            [
                'Content-Type' => [
                    'application/json',
                ],
            ],
            '{"domain":"value"}'
        );
    }

    public function testDomainArray()
    {
        $action = new Action(
            null,
            ['Radar\Adr\Fake\FakeDomain', '__invoke'],
            'Radar\Adr\Responder\Responder'
        );

        $this->assertResponse(
            $action,
            200,
            [
                'Content-Type' => [
                    'application/json',
                ],
            ],
            '{"domain":"value"}'
        );
    }

    public function testDomainObject()
    {
        $action = new Action(
            null,
            new \Radar\Adr\Fake\FakeDomain(),
            'Radar\Adr\Responder\Responder'
        );

        $this->assertResponse(
            $action,
            200,
            [
                'Content-Type' => [
                    'application/json',
                ],
            ],
            '{"domain":"value"}'
        );
    }

    public function testWithoutDomain()
    {
        $action = new Action(
            null,
            null,
            'Radar\Adr\Fake\Action\Responder'
        );

        $this->assertResponse(
            $action,
            200,
            [
            ],
            'No payload.'
        );
    }
}
