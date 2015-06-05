<?php
namespace Radar\Adr\Handler;

use Exception;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Radar\Adr\Sender;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $exceptionHandler = new ExceptionHandler(
            new Sender(['Radar\Adr\Fake\FakePhp', 'header'])
        );

        ob_start();
        $response = $exceptionHandler(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            function ($request, $response) {
                throw new Exception('Random exception');
            }
        );
        $actual = ob_get_clean();

        $this->assertEquals('Random exception', $actual);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
