<?php
namespace Radar\Adr\Fake;

use Psr\Http\Message\ResponseInterface;
use Radar\Adr\Sender;

class FakeSender extends Sender
{
    public $sent = false;

    public function send(ResponseInterface $response)
    {
        $this->sent = true;
    }
}
