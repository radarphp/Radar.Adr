<?php
namespace Radar\Adr\Handler;

use Phly\Http\Response;
use Phly\Http\ServerRequestFactory;
use Radar\Adr\Fake\FakeSender;
use Radar\Adr\Sender;

class SendingHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $fakeSender = new FakeSender();
        $sendingHandler = new SendingHandler($fakeSender);
        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $this->assertFalse($fakeSender->sent);
        $returnedResponse = $sendingHandler(
            $request,
            $response,
            function ($request, $response) { return $response; }
        );
        $this->assertTrue($fakeSender->sent);
        $this->assertSame($response, $returnedResponse);
    }
}
