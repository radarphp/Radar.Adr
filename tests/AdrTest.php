<?php
namespace Radar\Adr;

class AdrTest extends \PHPUnit_Framework_TestCase
{
    protected $adr;

    public function setup()
    {
        $this->adr = new Adr(new FakeDispatcher(new FakeMap()));
    }

    public function testProxyToMap()
    {
        $expect = 'Radar\Adr\FakeMap::fakeMapMethod';
        $actual = $this->adr->fakeMapMethod();
        $this->assertSame($expect, $actual);
    }

    public function testMiddles()
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
        $this->adr->error('errorInput', 'errorDomain', 'errorResponder');

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
            'error' => [
                'errorInput',
                'errorDomain',
                'errorResponder',
            ],
        ];

        $actual = $this->adr->getMiddles();
        $this->assertSame($expect, $actual);
    }

    public function testInvoke()
    {
        $expect = 'Radar\Adr\FakeDispatcher::__invoke';
        $actual = $this->adr->__invoke();
        $this->assertSame($expect, $actual);
    }
}
