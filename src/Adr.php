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
 * The "main" class for building and running the router and the middleware
 * relay.
 *
 * @package radar/adr
 *
 */
class Adr
{
    /**
     *
     * The router map.
     *
     * @var Map
     *
     */
    protected $map;

    /**
     *
     * A queue of middleware specifications.
     *
     * @var array
     *
     */
    protected $middle = [];

    /**
     *
     * The middleware relay builder.
     *
     * @var RelayBuilder
     *
     */
    protected $relayBuilder;

    /**
     *
     * The route-matching rules.
     *
     * @var RuleIterator
     *
     */
    protected $rules;

    /**
     *
     * Constructor.
     *
     * @param Map $map The router map.
     *
     * @param RuleIterator $rules The route-matching rules.
     *
     * @param RelayBuilder $relayBuilder The middleware relay builder.
     *
     */
    public function __construct(
        Map $map,
        RuleIterator $rules,
        RelayBuilder $relayBuilder
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->relayBuilder = $relayBuilder;
    }

    /**
     *
     * Proxies method calls to the router map.
     *
     * @param string $method The Map method to call.
     *
     * @param array $params The params to pass to the method.
     *
     * @return mixed
     *
     */
    public function __call($method, array $params)
    {
        return call_user_func_array([$this->map, $method], $params);
    }

    /**
     *
     * Returns the RuleIterator object.
     *
     * @return RuleIterator
     *
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     *
     * Adds a middleware specification to the queue.
     *
     * @param mixed $spec The middleware specification.
     *
     */
    public function middle($spec)
    {
        $this->middle[] = $spec;
    }

    /**
     *
     * Runs Radar using a Relay built with the middleware queue.
     *
     * @param Request $request The HTTP request object.
     *
     * @param Response $response The HTTP response object.
     *
     * @return Response
     *
     */
    public function run(Request $request, Response $response)
    {
        $relay = $this->relayBuilder->newInstance($this->middle);
        return $relay($request, $response);
    }
}
