<?php
namespace Radar\Adr\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Radar\Adr\Sender;

class SendingHandler
{
    protected $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function __invoke(
        Request $request,
        Response $response,
        callable $next
    ) {
        $this->sender->send($response);
        return $next($request, $response);
    }
}
