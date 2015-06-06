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

        $response = $exceptionHandler(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            function ($request, $response) {
                throw new Exception('Random exception');
            }
        );

        $this->assertEquals('Random exception', $response->getBody()->__toString());
        $this->assertEquals(500, $response->getStatusCode());
    }
}
