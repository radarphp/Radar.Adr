<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */

namespace Radar\Middleware;

/**
 * Auto-discovery of custom Action specs or fallback to the defaults.
 *
 * @package   radar\middleware
 */
class ActionSpecs
{
    /**
     *
     * The input spec to use with the action.
     *
     * @var string
     *
     */
    public $input = 'Radar\Middleware\Input';

    /**
     *
     * The domain spec to use with the action.
     *
     * @var string
     *
     */
    public $domain;

    /**
     *
     * The responder spec to use with the action.
     *
     * @var string
     *
     */
    public $responder = 'Radar\Middleware\Responder\Responder';

    /**
     * ActionSpecs constructor.
     *
     * @param string $handler
     */
    public function __construct(string $handler)
    {
        $input = $handler . '\\Input';
        if (class_exists($input)) {
            $this->input($input);
        }

        $this->domain($handler);

        $responder = $handler . '\\Responder';
        if (class_exists($responder)) {
            $this->responder($responder);
        }
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
    protected function input($input): self
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
    protected function domain($domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     *
     * Sets the responder specification.
     *
     * @param string $responder The responder specification.
     *
     * @return self
     *
     */
    protected function responder($responder): self
    {
        $this->responder = $responder;
        return $this;
    }
}
