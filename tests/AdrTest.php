<?php
namespace Radar\Adr;

use Aura\Router\Rule\RuleIterator;
use Radar\Adr\Router\Route;

class AdrTest extends \PHPUnit_Framework_TestCase
{
    protected $adr;

    public function setup()
    {
        $this->fakeMap = new Fake\FakeMap(new Route());
        $this->fakeRules = new RuleIterator();
        $this->fakeMiddle = new Fake\FakeMiddle();
        $this->fakeDispatcher = new Fake\FakeDispatcher($this->fakeMiddle);
        $this->adr = new Adr($this->fakeMap, $this->fakeRules, $this->fakeDispatcher);
    }

    public function testProxyToMap()
    {
        $expect = 'Radar\Adr\Fake\FakeMap::fakeMapMethod';
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
        $this->adr->actionHandler('Foo\Bar\ActionHandler');
        $this->adr->sendingHandler('Foo\Bar\SendingHandler');
        $this->adr->exceptionHandler('Foo\Bar\ExceptionHandler');

        $expect = 'Foo\Bar\ActionHandler';
        $this->assertSame($expect, $this->fakeDispatcher->actionHandler);

        $expect = 'Foo\Bar\RoutingHandler';
        $this->assertSame($expect, $this->fakeDispatcher->routingHandler);

        $expect = 'Foo\Bar\SendingHandler';
        $this->assertSame($expect, $this->fakeDispatcher->sendingHandler);

        $expect = 'Foo\Bar\ExceptionHandler';
        $this->assertSame($expect, $this->fakeDispatcher->exceptionHandler);

        $expect = [
            [
                'before1',
                'before2',
                'before3',
            ]
        ];
        $this->assertSame($expect, $this->fakeMiddle->before);

        $expect = [
            [
                'after1',
                'after2',
                'after3',
            ]
        ];
        $this->assertSame($expect, $this->fakeMiddle->after);

        $expect = [
            [
                'finish1',
                'finish2',
                'finish3',
            ]
        ];
        $this->assertSame($expect, $this->fakeMiddle->finish);
    }

    public function testInvoke()
    {
        $expect = 'Radar\Adr\Fake\FakeDispatcher::__invoke';
        $actual = $this->adr->__invoke();
        $this->assertSame($expect, $actual);
    }
}
