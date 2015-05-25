<?php
namespace Radar\Adr;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Sender
{
    public function send(ResponseInterface $response)
    {
        $this->sendStatus($response);
        $this->sendHeaders($response);
        $this->sendBody($response);
    }

    protected function sendStatus(ResponseInterface $response)
    {
        $version = $response->getProtocolVersion();
        $status = $response->getStatusCode();
        $phrase = $response->getReasonPhrase();
        header("HTTP/{$version} {$status} {$phrase}");
    }

    protected function sendHeaders(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $name => $values) {
            $this->sendHeader($name, $values);
        }
    }

    protected function sendHeader($name, $values)
    {
        $name = str_replace('-', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '-', $name);
        foreach ($values as $value) {
            header("{$name}: {$value}", false);
        }
    }

    protected function sendBody(ResponseInterface $response)
    {
        echo $response->getBody();
    }
}
