<?php
namespace Radar\Adr\Responder;

use Aura\Payload\Payload;
use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;

class ResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $responder;
    protected $payload;

    public function setup()
    {
        $this->responder = new Responder();
    }

    public function testAccepts()
    {
        $expect = ['application/json'];
        $actual = Responder::accepts();
        $this->assertSame($expect, $actual);
    }

    protected function getResponse($payload)
    {
        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        return $payload
            ? $this->responder->__invoke($request, $response, $payload)
            : $this->responder->__invoke($request, $response);
    }

    protected function assertPayloadResponse($payload, $status, array $headers, $body)
    {
        $response = $this->getResponse($payload);

        $this->assertEquals($status, $response->getStatusCode());

        foreach ($headers as $header => $expect) {
            $this->assertEquals((array) $expect, $response->getHeader($header));
        }

        ob_start();
        echo $response->getBody();
        $actual = ob_get_clean();

        $this->assertEquals($body, $actual);
    }

    public function testAccepted()
    {
        $payload = (new Payload())
            ->setStatus(Payload::ACCEPTED)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            202,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testCreated()
    {
        $payload = (new Payload())
            ->setStatus(Payload::CREATED)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            201,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testDeleted()
    {
        $payload = (new Payload())
            ->setStatus(Payload::DELETED)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            204,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testError()
    {
        $payload = (new Payload())
            ->setStatus(Payload::ERROR)
            ->setCode(96)
            ->setMessage('bad');

        $this->assertPayloadResponse(
            $payload,
            500,
            ['Content-Type' => 'application/json'],
            '{"code":96,"message":"bad"}'
        );
    }

    public function testFailure()
    {
        $payload = (new Payload())
            ->setStatus(Payload::FAILURE)
            ->setInput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            400,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testFound()
    {
        $payload = (new Payload())
            ->setStatus(Payload::FOUND)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            200,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testNoContent()
    {
        $this->assertPayloadResponse(
            null,
            204,
            [],
            ''
        );
    }

    public function testNotAuthenticated()
    {
        $payload = (new Payload())
            ->setStatus(Payload::NOT_AUTHENTICATED)
            ->setInput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            400,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testNotAuthorized()
    {
        $payload = (new Payload())
            ->setStatus(Payload::NOT_AUTHORIZED)
            ->setInput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            403,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testNotFound()
    {
        $payload = (new Payload())
            ->setStatus(Payload::NOT_FOUND)
            ->setInput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            404,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testNotValid()
    {
        $payload = (new Payload())
            ->setStatus(Payload::NOT_VALID)
            ->setInput(['foo' => 'bar'])
            ->setOutput(['baz' => 'dib'])
            ->setExtras(['zim' => 'gir']);

        $expect = json_encode([
            'input' => ['foo' => 'bar'],
            'output' => ['baz' => 'dib'],
            'errors' => ['zim' => 'gir'],
        ]);

        $this->assertPayloadResponse(
            $payload,
            422,
            ['Content-Type' => 'application/json'],
            $expect
        );
    }

    public function testProcessing()
    {
        $payload = (new Payload())
            ->setStatus(Payload::PROCESSING)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            203,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testSuccess()
    {
        $payload = (new Payload())
            ->setStatus(Payload::SUCCESS)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            200,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }

    public function testUnknown()
    {
        $payload = (new Payload())
            ->setStatus('foobar')
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            500,
            ['Content-Type' => 'application/json'],
            '{"error":"Unknown domain payload status","status":"foobar"}'
        );
    }

    public function testUpdated()
    {
        $payload = (new Payload())
            ->setStatus(Payload::UPDATED)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            303,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }
}
