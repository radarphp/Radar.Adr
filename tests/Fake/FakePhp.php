<?php
namespace Radar\Adr\Fake;

class FakePhp
{
    static public $headers = [];

    static public function header($string, $flag = null)
    {
        static::$headers[] = $string;
    }
}
