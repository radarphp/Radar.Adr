<?php
namespace Radar\Adr;

function header($string, $flag = true)
{
    Php::header($string, $flag);
}

class Php
{
    static public $headers = [];

    static public function header($string, $flag)
    {
        static::$headers[] = $string;
    }
}
