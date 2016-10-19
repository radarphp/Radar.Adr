<?php
namespace Radar\Adr\Fake\Action;

use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Radar\Adr\Responder\ResponderAcceptsInterface;

use Zend\Diactoros\Response;

class Responder implements ResponderAcceptsInterface
{
    public static function accepts()
    {
        return ['foo/bar'];
    }

    public function __construct()
    {
        $this->response = new Response;
    }

    public function __invoke(
        Request $request,
        PayloadInterface $payload = null
    ) {
        if ($payload) {
            $this->response->getBody()->write($payload->getOutput());
        } else {
            $this->response->getBody()->write('No payload.');
        }
        return $this->response;
    }
}
