<?php
namespace Radar\Adr\Handler;

use Exception;
use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $exceptionHandler = new ExceptionHandler();
        $response = $exceptionHandler(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            new Exception('Random exception')
        );

        $this->assertEquals(500, $response->getStatusCode());

        ob_start();
        echo $response->getBody();
        $actual = ob_get_clean();

        $this->assertEquals('Random exception', $actual);
    }
}
