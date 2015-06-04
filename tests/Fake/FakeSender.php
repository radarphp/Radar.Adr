<?php
namespace Radar\Adr\Fake;

use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Sender;

class FakeSender extends Sender
{
    public $sent = false;

    public function send(Response $response)
    {
        $this->sent = true;
    }
}
