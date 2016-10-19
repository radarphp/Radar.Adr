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
use Telegraph\TelegraphFactory;

/**
 *
 * The "main" class for building and running the router and the middleware
 * telegraph.
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
     * The middleware telegraph factory.
     *
     * @var TelegraphFactory
     *
     */
    protected $telegraphFactory;

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
     * The rresolver
     *
     * @var callable
     *
     */
    protected $resolver;

    /**
     *
     * Constructor.
     *
     * @param Map $map The router map.
     *
     * @param RuleIterator $rules The route-matching rules.
     *
     * @param TelegraphFactory $telegraphFactory The middleware telegraph builder.
     *
     * @param callable $resolver The resolver.
     *
     */
    public function __construct(
        Map $map,
        RuleIterator $rules,
        TelegraphFactory $telegraphFactory,
        callable $resolver = null
    ) {
        $this->map = $map;
        $this->rules = $rules;
        $this->telegraphFactory = $telegraphFactory;
        $this->resolver = $resolver;
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
    public function run(Request $request)
    {
        $telegraph = $this->telegraphFactory->newInstance(
            $this->middle, $this->resolver
        );
        return $telegraph->dispatch($request);
    }
}
