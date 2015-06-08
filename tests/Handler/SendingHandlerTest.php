<?php
namespace Radar\Adr\Handler;

use Radar\Adr\Fake\FakePhp;
use Radar\Adr\Fake\FakeSender;
use Radar\Adr\Sender;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

function header($string, $flag = null)
{
    FakePhp::header($string, $flag);
}

class SendingHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        FakePhp::$headers = [];

        ob_start();
        $sender = new SendingHandler();
        $response = $sender(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            function ($request, $response) {
                $response = $response->withHeader('content-type', 'foo/bar');
                $response->getBody()->write('DOOM');
                return $response;
            }
        );
        $body = ob_get_clean();

        $expect = [
            'HTTP/1.1 200 OK',
            'Content-Type: foo/bar'
        ];
        $this->assertSame($expect, FakePhp::$headers);
        $this->assertSame('DOOM', $body);
    }
}
