<?php
namespace Radar\Adr;

use Radar\Adr\Fake\FakePhp;
use Radar\Adr\Fake\FakeWare;
use Radar\Adr\Fake\FakeDomain;

function header($string, $flag = true)
{
    FakePhp::header($string, $flag);
}

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    protected function setUp()
    {
        FakePhp::$headers = [];
        FakeWare::$count = 0;
    }

    protected function newAdr(array $server = [])
    {
        FakePhp::$headers = [];
        FakeWare::$count = 0;
        $_SERVER = array_merge($_SERVER, $server);
        $boot = new Boot(__DIR__ . DIRECTORY_SEPARATOR . '_env');
        return $boot();
    }

    protected function assertOutput($adr, $expectHeaders, $expectBody)
    {
        ob_start();
        $adr();
        $actualBody = ob_get_clean();
        $this->assertEquals($expectBody, $actualBody);
        $this->assertEquals($expectHeaders, FakePhp::$headers);
    }

    public function testOk()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->get('Radar\Adr\Fake\Action', '/fake', 'Radar\Adr\Fake\FakeDomain');
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 200 OK',
            ],
            'domain'
        );
    }

    public function testOkWithArrayDomain()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->get('Radar\Adr\Fake\Action', '/fake', ['Radar\Adr\Fake\FakeDomain', '__invoke']);
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 200 OK',
            ],
            'domain'
        );
    }

    public function testOkWithObjectDomain()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->get('Radar\Adr\Fake\Action', '/fake', new Fake\FakeDomain());
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 200 OK',
            ],
            'domain'
        );
    }

    public function testInboundError()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/no-such-path',
        ]);

        $adr->get('Radar\Adr\Fake\Action', '/fake', 'Radar\Adr\Fake\FakeDomain');
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 404 Not Found',
            ],
            '404 Not Found'
        );
    }

    public function testOutboundErrorInAfter()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->after('Radar\Adr\Fake\FakeWareError');

        $adr->get('Radar\Adr\Fake\Action', '/fake', 'Radar\Adr\Fake\FakeDomain');
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 500 Internal Server Error',
            ],
            'domainError in middle'
        );
    }

    public function testOutboundErrorInFinish()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->finish('Radar\Adr\Fake\FakeWareError');
        $adr->get('Radar\Adr\Fake\Action', '/fake', 'Radar\Adr\Fake\FakeDomain');

        // OK, because the error is after the response is sent
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 200 OK',
            ],
            'domain'
        );
    }

    public function testMiddle()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->before('Radar\Adr\Fake\FakeWare');
        $adr->before('Radar\Adr\Fake\FakeWare');
        $adr->before('Radar\Adr\Fake\FakeWare');

        $adr->get('Radar\Adr\Fake\Action', '/fake', new FakeDomain());
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 200 OK',
            ],
            'domain 3'
        );
        $this->assertSame(3, FakeWare::$count);

    }

    public function testMiddleError()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->before('Radar\Adr\Fake\FakeWare');
        $adr->before('Radar\Adr\Fake\FakeWare');
        $adr->before('Radar\Adr\Fake\FakeWareError');

        $adr->get('Radar\Adr\Fake\Action', '/fake', new FakeDomain());
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 500 Internal Server Error',
            ],
            'Error in middle'
        );
        $this->assertSame(2, FakeWare::$count);
    }

    public function testMiddleEarly()
    {
        $adr = $this->newAdr([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/fake',
        ]);

        $adr->before('Radar\Adr\Fake\FakeWare');
        $adr->before('Radar\Adr\Fake\FakeWareEarly');
        $adr->before('Radar\Adr\Fake\FakeWare');

        $adr->get('Radar\Adr\Fake\Action', '/fake', new FakeDomain());
        $this->assertOutput(
            $adr,
            [
                'HTTP/1.1 200 OK',
            ],
            'Early exit in middle'
        );
        $this->assertSame(1, FakeWare::$count);
    }
}
