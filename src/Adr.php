<?php
namespace Radar\Adr;

use Aura\Router\Rule\RuleIterator;
use Pipeline\Pipeline\PipelineBuilder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Router\Map;

class Adr
{
    protected $pipelineBuilder;
    protected $map;
    protected $middle = [];
    protected $rules;

    public function __construct(
        Map $map,
        RuleIterator $rules,
        PipelineBuilder $pipelineBuilder
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->pipelineBuilder = $pipelineBuilder;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    public function rules()
    {
        return $this->rules;
    }

    public function middle($spec)
    {
        return $this->middle[] = $spec;
    }

    public function run(Request $request, Response $response)
    {
        $pipeline = $this->pipelineBuilder->newInstance($this->middle);
        return $pipeline($request, $response);
    }
}
