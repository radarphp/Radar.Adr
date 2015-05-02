<?php
namespace Radar\Adr;

use Phly\Http\Response;

// namespace-specific header() function
function header($string, $flag = true)
{
    SenderTest::header($string, $flag);
}

class SenderTest extends \PHPUnit_Framework_TestCase
{
    protected static $headers = [];

    static public function header($string, $flag)
    {
        static::$headers[] = $string;
    }

    public function test()
    {
        $sender = new Sender();

        $response = new Response();
        $response = $response->withHeader('content-type', 'foo/bar');
        $response->getBody()->write('DOOM');

        ob_start();
        $sender($response);
        $body = ob_get_clean();

        $expect = [
            'HTTP/1.1 200 OK',
            'Content-Type: foo/bar'
        ];
        $this->assertSame($expect, static::$headers);
        $this->assertSame('DOOM', $body);
    }
}
