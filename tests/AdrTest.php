<?php
namespace Radar\Adr;

class AdrTest extends \PHPUnit_Framework_TestCase
{
    protected $adr;

    public function setup()
    {
        $this->adr = new Adr(
            new FakeMap(new Route()),
            new FakeDispatcher()
        );
    }

    public function testProxyToMap()
    {
        $expect = 'Radar\Adr\FakeMap::fakeMapMethod';
        $actual = $this->adr->fakeMapMethod();
        $this->assertSame($expect, $actual);
    }

    public function testGetDispatcherParams()
    {
        $this->adr->before('before1');
        $this->adr->before('before2');
        $this->adr->before('before3');
        $this->adr->after('after1');
        $this->adr->after('after2');
        $this->adr->after('after3');
        $this->adr->finish('finish1');
        $this->adr->finish('finish2');
        $this->adr->finish('finish3');
        $this->adr->routingHandler('Foo\Bar\RoutingHandler');
        $this->adr->sendingHandler('Foo\Bar\SendingHandler');
        $this->adr->exceptionHandler('Foo\Bar\ExceptionHandler');

        $expect = [
            'before' => [
                'before1',
                'before2',
                'before3',
            ],
            'after' => [
                'after1',
                'after2',
                'after3',
            ],
            'finish' => [
                'finish1',
                'finish2',
                'finish3',
            ],
            'routingHandler' => 'Foo\Bar\RoutingHandler',
            'sendingHandler' => 'Foo\Bar\SendingHandler',
            'exceptionHandler' => 'Foo\Bar\ExceptionHandler',
        ];

        $actual = $this->adr->getDispatcherParams();
        $this->assertSame($expect, $actual);
    }

    public function testInvoke()
    {
        $expect = 'Radar\Adr\FakeDispatcher::__invoke';
        $actual = $this->adr->__invoke();
        $this->assertSame($expect, $actual);
    }
}
