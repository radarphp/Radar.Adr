<?php
namespace Radar\Adr\FakeAction;

use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\ResponderInterface;

class Responder implements ResponderInterface
{
    public static function getMediaTypes()
    {
        return ['foo/bar'];
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        PayloadInterface $payload = null
    ) {
        if ($payload) {
            $response->getBody()->write($payload->getOutput());
        }
        return $response;
    }
}
