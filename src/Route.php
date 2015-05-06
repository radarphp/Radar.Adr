<?php
namespace Radar\Adr;

use Aura\Router\Route as AuraRoute;

class Route extends AuraRoute
{
    protected $input = 'Radar\Adr\Input';
    protected $domain;
    protected $responder = 'Radar\Adr\Responder';

    public function input($input)
    {
        $this->input = $input;
    }

    public function domain($domain)
    {
        $this->domain = $domain;
    }

    public function responder($responder)
    {
        $this->responder = $responder;
        if (is_subclass_of($responder, 'Radar\Adr\ResponderInterface', true)) {
            $this->accepts = [];
            $this->accepts($responder::getMediaTypes());
        }
    }
}
