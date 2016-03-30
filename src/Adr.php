<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Aura\Router\Map;
use Aura\Router\Rule\RuleIterator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Relay\RelayBuilder;

/**
 *
 * The "core" class for building and running the router and the middleware
 * relay.
 *
 * @package radar/adr
 *
 */
class Adr
{
    protected $relayBuilder;
    protected $map;
    protected $middle = [];
    protected $rules;

    public function __construct(
        Map $map,
        RuleIterator $rules,
        RelayBuilder $relayBuilder
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->relayBuilder = $relayBuilder;
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
        $relay = $this->relayBuilder->newInstance($this->middle);
        return $relay($request, $response);
    }
}
