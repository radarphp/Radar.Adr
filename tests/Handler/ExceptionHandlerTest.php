<?php
namespace Radar\Adr\Handler;

use Exception;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $exceptionHandler = new ExceptionHandler();

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
