<?php
namespace Radar\Adr\Responder;

use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

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
            ->setStatus(PayloadStatus::ACCEPTED)
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
            ->setStatus(PayloadStatus::CREATED)
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
            ->setStatus(PayloadStatus::DELETED)
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
            ->setStatus(PayloadStatus::ERROR)
            ->setInput(['foo' => 'bar'])
            ->setOutput('96: wrong');

        $this->assertPayloadResponse(
            $payload,
            500,
            ['Content-Type' => 'application/json'],
            '{"input":{"foo":"bar"},"error":"96: wrong"}'
        );
    }

    public function testFailure()
    {
        $payload = (new Payload())
            ->setStatus(PayloadStatus::FAILURE)
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
            ->setStatus(PayloadStatus::FOUND)
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
            ->setStatus(PayloadStatus::NOT_AUTHENTICATED)
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
            ->setStatus(PayloadStatus::NOT_AUTHORIZED)
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
            ->setStatus(PayloadStatus::NOT_FOUND)
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
            ->setStatus(PayloadStatus::NOT_VALID)
            ->setInput(['foo' => 'bar'])
            ->setOutput(['baz' => 'dib'])
            ->setMessages(['zim' => 'gir']);

        $expect = json_encode([
            'input' => ['foo' => 'bar'],
            'output' => ['baz' => 'dib'],
            'messages' => ['zim' => 'gir'],
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
            ->setStatus(PayloadStatus::PROCESSING)
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
            ->setStatus(PayloadStatus::SUCCESS)
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
            ->setStatus(PayloadStatus::UPDATED)
            ->setOutput(['foo' => 'bar']);

        $this->assertPayloadResponse(
            $payload,
            303,
            ['Content-Type' => 'application/json'],
            '{"foo":"bar"}'
        );
    }
}
