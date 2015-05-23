<?php
namespace Radar\Adr;

use Phly\Http\Response;
use Radar\Adr\Fake\FakePhp;

function header($string, $flag = null)
{
    FakePhp::header($string, $flag);
}

class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        FakePhp::$headers = [];
        $sender = new Sender();
        $response = new Response();
        $response = $response->withHeader('content-type', 'foo/bar');
        $response->getBody()->write('DOOM');

        ob_start();
        $sender->send($response);
        $body = ob_get_clean();

        $expect = [
            'HTTP/1.1 200 OK',
            'Content-Type: foo/bar'
        ];
        $this->assertSame($expect, FakePhp::$headers);
        $this->assertSame('DOOM', $body);
    }
}
