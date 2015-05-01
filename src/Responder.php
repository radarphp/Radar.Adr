<?php
namespace Radar\Adr;

use Aura\Payload_Interface\PayloadInterface;
use Aura\Payload\Payload;
use Aura\Router\Generator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Responder implements ResponderInterface
{
    protected $request;

    protected $response;

    protected $payload;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public static function getMediaTypes()
    {
        return ['application/json'];
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        PayloadInterface $payload = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->payload = $payload;
        $this->runMethodForPayload();
        return $this->response;
    }

    protected function runMethodForPayload()
    {
        if (! $this->payload) {
            return $this->noContent();
        }

        $method = str_replace('_', '', $this->payload->getStatus());
        if (! method_exists($this, $method)) {
            $method = 'unknown';
        }
        return $this->$method();
    }

    protected function writeJson($data)
    {
        $this->response = $this->response->withHeader('Content-Type', 'application/json');
        $this->response->getBody()->write(json_encode($data));
    }

    protected function unknown()
    {
        $this->response = $this->response->withStatus(500);
        $this->writeJson([
            'error' => 'Unknown domain payload status',
            'status' => $this->payload->getStatus(),
        ]);
    }

    protected function accepted()
    {
        $this->response = $this->response->withStatus(202);
        $this->writeJson($this->payload->getOutput());
    }

    protected function created()
    {
        $this->response = $this->response->withStatus(201);
        $this->writeJson($this->payload->getOutput());
    }

    protected function deleted()
    {
        $this->response = $this->response->withStatus(204);
        $this->writeJson($this->payload->getOutput());
    }

    protected function error()
    {
        $this->response = $this->response->withStatus(500);
        $this->writeJson([
            'code' => $this->payload->getCode(),
            'message' => $this->payload->getMessage(),
        ]);
    }

    protected function failure()
    {
        $this->response = $this->response->withStatus(400);
        $this->writeJson($this->payload->getInput());
    }

    protected function found()
    {
        $this->response = $this->response->withStatus(200);
        $this->writeJson($this->payload->getOutput());
    }

    protected function noContent()
    {
        $this->response = $this->response->withStatus(204);
    }

    protected function notAuthenticated()
    {
        $this->response = $this->response->withStatus(400);
        $this->writeJson($this->payload->getOutput());
    }

    protected function notAuthorized()
    {
        $this->response = $this->response->withStatus(403);
        $this->writeJson($this->payload->getOutput());
    }

    protected function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->writeJson($this->payload->getInput());
    }

    protected function notValid()
    {
        $this->response = $this->response->withStatus(422);
        $this->writeJson([
            'input' => $this->payload->getInput(),
            'errors' => $this->payload->getExtras(),
        ]);
    }

    protected function processing()
    {
        $this->response = $this->response->withStatus(203);
        $this->writeJson($this->payload->getOutput());
    }

    protected function success()
    {
        $this->response = $this->response->withStatus(200);
        $this->writeJson($this->payload->getOutput());
    }

    protected function updated()
    {
        $this->response = $this->response->withStatus(303);
        $this->writeJson($this->payload->getOutput());
    }
}
