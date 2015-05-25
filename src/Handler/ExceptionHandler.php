<?php
namespace Radar\Adr\Handler;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Sender;

class ExceptionHandler
{
    protected $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Exception $exception
    ) {
        $response = $response->withStatus(500);
        $response->getBody()->write($exception->getMessage());
        $this->sender->send($response);
        return $response;
    }
}
