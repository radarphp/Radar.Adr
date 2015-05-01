<?php
namespace Radar\Adr\Error;

use Aura\Payload\Payload;

class Domain
{
    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
    }

    public function __invoke($input)
    {
        return $this->payload
            ->setStatus(Payload::ERROR)
            ->setOutput($input['radar/adr:exception']);
    }
}
