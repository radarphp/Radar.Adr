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
    protected $input = 'Radar\Adr\Input';
    protected $domain;
    protected $responder = 'Radar\Adr\Responder\Responder';

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

    public function handler($handler)
    {
        $this->domain($handler);
        return $this;
    }

    public function input($input)
    {
        $this->input = $input;
        return $this;
    }

    public function domain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function responder($responder)
    {
        $this->responder = $responder;
        $this->accepts = [];

        $responderAcceptsInterface = is_subclass_of(
            $responder,
            'Radar\Adr\Responder\ResponderAcceptsInterface',
            true
        );

        if ($responderAcceptsInterface) {
            $this->accepts($responder::accepts());
        }

        return $this;
    }
}
