<?php
namespace Radar\Adr;

class Php
{
    static public $headers = [];

    static public function header($string, $flag)
    {
        static::$headers[] = $string;
    }
}
