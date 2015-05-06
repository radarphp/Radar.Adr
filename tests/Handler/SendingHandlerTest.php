<?php
namespace Radar\Adr\Handler;

use Phly\Http\Response;
use Radar\Adr\Fake\FakePhp;

function header($string, $flag = true)
{
    FakePhp::header($string, $flag);
}

class SenderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        FakePhp::$headers = [];
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
        $this->assertSame($expect, FakePhp::$headers);
        $this->assertSame('DOOM', $body);
    }
}
