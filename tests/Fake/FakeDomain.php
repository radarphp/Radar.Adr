<?php
namespace Radar\Adr\Fake;

use Aura\Payload\Payload;

class FakeDomain
{
    public function __invoke($input)
    {
        $output = 'domain';
        if (isset($input['radar/adr:test-middle'])) {
            $output .= ' ' . $input['radar/adr:test-middle'];
        }

        $payload = new Payload();
        return $payload
            ->setStatus(Payload::FOUND)
            ->setOutput($output);
    }
}
