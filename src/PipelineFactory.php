<?php
namespace Radar\Adr;

use Pipeline\Pipeline\Pipeline;

class PipelineFactory
{
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function newInstance(array $queue)
    {
        return new Pipeline($queue, $this->resolver);
    }
}
