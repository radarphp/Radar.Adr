<?php
namespace Radar\Adr;

use Phly\Http\Response;

class SenderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Php::$headers = [];
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
        $this->assertSame($expect, Php::$headers);
        $this->assertSame('DOOM', $body);
    }
}
