<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware\Responder;

use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 * A generic Responder.
 *
 * @package radar/middleware
 *
 */
class Responder
{
    /**
     *
     * The domain payload (i.e. the output from the domain).
     *
     * @var PayloadInterface
     *
     */
    protected $payload;

    /**
     *
     * The HTTP request.
     *
     * @var Request
     *
     */
    protected $request;

    /**
     *
     * The HTTP response.
     *
     * @var Response
     *
     */
    protected $response;

    /**
     *
     * Builds and returns the Response using the Request and Payload.
     *
     * @param Request $request The HTTP request object.
     *
     * @param Response $response The HTTP response object.
     *
     * @param PayloadInterface $payload The domain payload object.
     *
     * @return Response
     *
     */
    public function __invoke(
        Request $request,
        Response $response,
        PayloadInterface $payload = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->payload = $payload;
        $method = $this->getMethodForPayload();
        $this->$method();
        return $this->response;
    }

    /**
     *
     * Returns the Responder method to call, based on the Payload status.
     *
     * @return string
     *
     */
    protected function getMethodForPayload()
    {
        if (! $this->payload) {
            return 'noContent';
        }

        $method = str_replace('_', '', strtolower($this->payload->getStatus()));
        return method_exists($this, $method) ? $method : 'unknown';
    }

    /**
     *
     * Encodes data into the Response body as JSON.
     *
     * @param mixed $data The data to encode.
     *
     */
    protected function jsonBody($data)
    {
        if (isset($data)) {
            $this->response = $this->response->withHeader('Content-Type', 'application/json');
            $this->response->getBody()->write(json_encode($data));
        }
    }

    /**
     *
     * Builds a Response for PayloadStatus::ACCEPTED.
     *
     */
    protected function accepted()
    {
        $this->response = $this->response->withStatus(202);
        $this->jsonBody($this->payload->getOutput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::CREATED.
     *
     */
    protected function created()
    {
        $this->response = $this->response->withStatus(201);
        $this->jsonBody($this->payload->getOutput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::DELETED.
     *
     */
    protected function deleted()
    {
        $this->response = $this->response->withStatus(204);
        $this->jsonBody($this->payload->getOutput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::ERROR.
     *
     */
    protected function error()
    {
        $this->response = $this->response->withStatus(500);
        $this->jsonBody([
            'input' => $this->payload->getInput(),
            'error' => $this->payload->getOutput(),
        ]);
    }

    /**
     *
     * Builds a Response for PayloadStatus::FAILURE.
     *
     */
    protected function failure()
    {
        $this->response = $this->response->withStatus(400);
        $this->jsonBody($this->payload->getInput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::FOUND.
     *
     */
    protected function found()
    {
        $this->response = $this->response->withStatus(200);
        $this->jsonBody($this->payload->getOutput());
    }

    /**
     *
     * Builds a Response when there was no Payload.
     *
     */
    protected function noContent()
    {
        $this->response = $this->response->withStatus(204);
    }

    /**
     *
     * Builds a Response for PayloadStatus::NOT_AUTHENTICATED.
     *
     */
    protected function notAuthenticated()
    {
        $this->response = $this->response->withStatus(401);
        $this->jsonBody($this->payload->getInput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::NOT_AUTHORIZED.
     *
     */
    protected function notAuthorized()
    {
        $this->response = $this->response->withStatus(403);
        $this->jsonBody($this->payload->getInput());
    }

    /**
     *
     * Builds a Response for PayloadStatus:: NOT_FOUND.
     *
     */
    protected function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->jsonBody($this->payload->getInput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::NOT_VALID.
     *
     */
    protected function notValid()
    {
        $this->response = $this->response->withStatus(422);
        $this->jsonBody([
            'input' => $this->payload->getInput(),
            'output' => $this->payload->getOutput(),
            'messages' => $this->payload->getMessages(),
        ]);
    }

    /**
     *
     * Builds a Response for PayloadStatus::PROCESSING.
     *
     */
    protected function processing()
    {
        $this->response = $this->response->withStatus(203);
        $this->jsonBody($this->payload->getOutput());
    }

    /**
     *
     * Builds a Response for PayloadStatus::SUCCESS.
     *
     */
    protected function success()
    {
        $this->response = $this->response->withStatus(200);
        $this->jsonBody($this->payload->getOutput());
    }

    /**
     *
     * Builds a Response when the payload status is not recognized.
     *
     */
    protected function unknown()
    {
        $this->response = $this->response->withStatus(500);
        $this->jsonBody([
            'error' => 'Unknown domain payload status',
            'status' => $this->payload->getStatus(),
        ]);
    }

    /**
     *
     * Builds a Response for PayloadStatus::UPDATED.
     *
     */
    protected function updated()
    {
        $this->response = $this->response->withStatus(303);
        $this->jsonBody($this->payload->getOutput());
    }
}
