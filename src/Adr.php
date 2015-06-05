<?php
namespace Radar\Adr;

use Aura\Router\Rule\RuleIterator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Router\Map;

class Adr
{
    protected $pipelineFactory;
    protected $map;
    protected $middle = [];
    protected $rules;

    public function __construct(
        Map $map,
        RuleIterator $rules,
        PipelineFactory $pipelineFactory
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->pipelineFactory = $pipelineFactory;
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
        $pipeline = $this->pipelineFactory->newInstance($this->middle);
        return $pipeline($request, $response);
    }
}
