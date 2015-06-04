<?php
namespace Radar\Adr\Fake\Action;

use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Responder\ResponderAcceptsInterface;

class Responder implements ResponderAcceptsInterface
{
    public static function accepts()
    {
        return ['foo/bar'];
    }

    public function __invoke(
        Request $request,
        Response $response,
        PayloadInterface $payload = null
    ) {
        if ($payload) {
            $response->getBody()->write($payload->getOutput());
        } else {
            $response->getBody()->write('No payload.');
        }
        return $response;
    }
}
