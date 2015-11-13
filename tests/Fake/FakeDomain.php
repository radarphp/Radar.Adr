<?php
namespace Radar\Adr\Fake;

use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class FakeDomain
{
    public function __invoke()
    {
        $payload = new Payload();
        return $payload
            ->setStatus(PayloadStatus::FOUND)
            ->setOutput(['domain' => 'value']);
    }
}
