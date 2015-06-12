<?php
namespace Radar\Adr;

class Action
{
    protected $input;
    protected $domain;
    protected $responder;

    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function setResponder($responder)
    {
        $this->responder = $responder;
        return $this;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getResponder()
    {
        return $this->responder;
    }
}
