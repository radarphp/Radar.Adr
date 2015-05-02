<?php
namespace Radar\Adr;

use Aura\Payload\Payload;
use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    protected $responder;

    public function setup()
    {
        $this->domain = new Error\Domain(new Payload());
        $this->responder = new Error\Responder();
    }

    protected function getResponse($e)
    {
        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $payload = $this->domain->__invoke([
            'radar/adr:exception' => $e
        ]);
        return $this->responder->__invoke($request, $response, $payload);
    }

    protected function assertPayloadResponse($e, $status, array $headers, $body)
    {
        $response = $this->getResponse($e);

        $this->assertEquals($status, $response->getStatusCode());

        foreach ($headers as $header => $expect) {
            $this->assertEquals((array) $expect, $response->getHeader($header));
        }

        ob_start();
        echo $response->getBody();
        $actual = ob_get_clean();

        $this->assertEquals($body, $actual);
    }

    public function testMethodNotAllowed()
    {
        $failedRoute = (new Route())
            ->allows(['PUT', 'POST'])
            ->failedRule('Aura\Router\Rule\Allows');

        $e = new Exception\RoutingFailed();
        $e->setFailedRoute($failedRoute);

        $this->assertPayloadResponse(
            $e,
            405,
            [
                'Allow' => 'PUT, POST',
                'Content-Type' => 'application/json'
            ],
            '["PUT","POST"]'
        );
    }

    public function testNotAcceptable()
    {
        $failedRoute = (new Route())
            ->accepts(['foo/bar', 'baz/dib'])
            ->failedRule('Aura\Router\Rule\Accepts');

        $e = new Exception\RoutingFailed();
        $e->setFailedRoute($failedRoute);

        $this->assertPayloadResponse(
            $e,
            406,
            [],
            '["foo\/bar","baz\/dib"]'
        );
    }

    public function testNotFound()
    {
        $failedRoute = (new Route())
            ->failedRule('Aura\Router\Rule\Path');

        $e = new Exception\RoutingFailed();
        $e->setFailedRoute($failedRoute);

        $this->assertPayloadResponse(
            $e,
            404,
            [],
            '404 Not Found'
        );
    }

    public function testUnknown()
    {
        $e = new Exception('Unknown error');
        $this->assertPayloadResponse(
            $e,
            500,
            [],
            'Unknown error'
        );
    }
}
