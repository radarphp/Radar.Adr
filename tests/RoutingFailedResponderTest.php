<?php
namespace Radar\Adr;

use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;

class RoutingFailedResponderTest extends \PHPUnit_Framework_TestCase
{
    protected function getResponse($failedRoute)
    {
        $routingFailedResponder = new RoutingFailedResponder();
        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        return $routingFailedResponder($request, $response, $failedRoute);
    }

    protected function assertResponse($failedRoute, $status, array $headers, $body)
    {
        $response = $this->getResponse($failedRoute);

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

        $this->assertResponse(
            $failedRoute,
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

        $this->assertResponse(
            $failedRoute,
            406,
            [],
            '["foo\/bar","baz\/dib"]'
        );
    }

    public function testNotFound()
    {
        $failedRoute = (new Route())
            ->failedRule('Aura\Router\Rule\Path');

        $this->assertResponse(
            $failedRoute,
            404,
            [],
            '404 Not Found'
        );
    }

    public function testUnknown()
    {
        $failedRoute = (new Route())
            ->name('test')
            ->failedRule('RandomRuleName');

        $this->assertResponse(
            $failedRoute,
            500,
            [],
            'Route test failed rule RandomRuleName'
        );
    }
}
