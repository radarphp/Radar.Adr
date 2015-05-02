<?php
namespace Radar\Adr;

use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;
use Aura\Payload\Payload;

class ResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $responder;
    protected $payload;

    public function setup()
    {
        $this->responder = new Responder();
    }

    public function getResponse($payload)
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

    public function testNoContent()
    {
        $this->assertPayloadResponse(
            null,
            204,
            [],
            ''
        );
    }
}
