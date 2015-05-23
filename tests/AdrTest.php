<?php
namespace Radar\Adr;

use Aura\Router\Rule\RuleIterator;
use Radar\Adr\Router\Route;
use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;

class AdrTest extends \PHPUnit_Framework_TestCase
{
    protected $adr;

    public function setup()
    {
        $this->fakeMap = new Fake\FakeMap(new Route());
        $this->fakeRules = new RuleIterator();
        $this->fakeHandlers = new Fake\FakeHandlers();
        $this->fakeDispatcherFactory = function ($handlers) {
            return new Fake\FakeDispatcher($handlers);
        };
        $this->adr = new Adr(
            $this->fakeMap,
            $this->fakeRules,
            $this->fakeHandlers,
            $this->fakeDispatcherFactory
        );
    }

    public function testProxyToMap()
    {
        $expect = 'Radar\Adr\Fake\FakeMap::fakeMapMethod';
        $actual = $this->adr->fakeMapMethod();
        $this->assertSame($expect, $actual);
    }

    public function testGetDispatcherParams()
    {
        $this->adr->middle('middle1');
        $this->adr->middle('middle2');
        $this->adr->middle('middle3');
        $this->adr->exceptionHandler('Foo\Bar\ExceptionHandler');

        $expect = 'Foo\Bar\ExceptionHandler';
        $this->assertSame($expect, $this->fakeHandlers->exceptionHandler);

        $expect = [
            'middle1',
            'middle2',
            'middle3',
        ];
        $this->assertSame($expect, $this->fakeHandlers->middle);
    }

    public function testRun()
    {
        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $expect = 'Radar\Adr\Fake\FakeDispatcher::__invoke';
        $actual = $this->adr->run($request, $response);
        $this->assertSame($expect, $actual);
    }
}
