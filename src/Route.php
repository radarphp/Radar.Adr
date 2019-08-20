<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Aura\Router\Route as AuraRoute;

/**
 *
 * A Radar-specific route definition.
 *
 * @package radar/adr
 *
 */
class Route extends AuraRoute
{
    /**
     *
     * The input spec to use with the action.
     *
     * @var string
     *
     */
    protected $input = Input::class;

    /**
     *
     * The domain spec to use with the action.
     *
     * @var string
     *
     */
    protected $domain;

    /**
     *
     * The responder spec to use with the action.
     *
     * @var string
     *
     */
    protected $responder = Responder\Responder::class;

    /**
     *
     * Sets the route name, typically a prefix for Input and Responder class
     * names; also sets the $input and $responder class names, if they exist.
     *
     * @param string $name The route name.
     *
     * @return self
     *
     */
    public function name($name)
    {
        parent::name($name);

        $input = $this->name . '\\Input';
        if (class_exists($input)) {
            $this->input($input);
        }

        $responder = $this->name . '\\Responder';
        if (class_exists($responder)) {
            $this->responder($responder);
        }

        return $this;
    }

    /**
     *
     * Overrides `parent::handler()` to set the domain specification.
     *
     * @param string $handler The domain specification.
     *
     * @return self
     *
     */
    public function handler($handler)
    {
        $this->domain($handler);
        return $this;
    }

    /**
     *
     * Sets the input specification.
     *
     * @param string $input The input specification.
     *
     * @return self
     *
     */
    public function input($input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     *
     * Sets the domain specification.
     *
     * @param string $domain The domain specification.
     *
     * @return self
     *
     */
    public function domain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     *
     * Sets the responder specification; if the responder is an instance of
     * ResponderAcceptsInterface, also sets the `accepts()` on the route.
     *
     * @param string $responder The responder specification.
     *
     * @return self
     *
     */
    public function responder($responder)
    {
        $this->responder = $responder;
        $this->accepts = [];

        $responderAcceptsInterface = is_subclass_of(
            $responder,
            Responder\ResponderAcceptsInterface::class,
            true
        );

        if ($responderAcceptsInterface) {
            $this->accepts($responder::accepts());
        }

        return $this;
    }
}
