<?php
namespace Radar\Adr\Handler;

use Phly\Http\Response;
use Radar\Adr\Php;

function header($string, $flag = true)
{
    Php::header($string, $flag);
}

class SenderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Php::$headers = [];
    }

    public function test()
    {
        $sendingHandler = new SendingHandler();

        $response = new Response();
        $response = $response->withHeader('content-type', 'foo/bar');
        $response->getBody()->write('DOOM');

        ob_start();
        $sendingHandler($response);
        $body = ob_get_clean();

        $expect = [
            'HTTP/1.1 200 OK',
            'Content-Type: foo/bar'
        ];
        $this->assertSame($expect, Php::$headers);
        $this->assertSame('DOOM', $body);
    }
}
