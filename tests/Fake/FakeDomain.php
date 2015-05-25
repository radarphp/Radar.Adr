<?php
namespace Radar\Adr\Fake;

use Aura\Payload\Payload;

class FakeDomain
{
    public function __invoke($input)
    {
        $payload = new Payload();
        return $payload
            ->setStatus(Payload::FOUND)
            ->setOutput(['domain' => 'value']);
    }
}
