<?php
namespace Radar\Adr;

class ActionFactory
{
    public function newInstance($input, $domain, $responder)
    {
        return new Action($input, $domain, $responder);
    }
}
