<?php
namespace Radar\Adr;

class FakePhp
{
    static public $headers = [];

    static public function header($string, $flag)
    {
        static::$headers[] = $string;
    }
}
