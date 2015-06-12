<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use Aura\Router\Rule\RuleIterator;
use Relay\RelayBuilder;
use Radar\Adr\Router\Route;
use Radar\Adr\Fake\FakeMiddleware;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class AdrTest extends \PHPUnit_Framework_TestCase
{
    protected $adr;

    public function setUp()
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance();
        $resolver = new Resolver($di->getInjectionFactory());

        $this->fakeMap = new Fake\FakeMap(new Route());
        $this->fakeRules = new RuleIterator();
        $this->relayBuilder = new RelayBuilder($resolver);

        $this->adr = new Adr(
            $this->fakeMap,
            $this->fakeRules,
            $this->relayBuilder
        );
    }

    public function testRules()
    {
        $this->assertSame($this->adr->rules(), $this->fakeRules);
    }

    public function testProxyToMap()
    {
        $expect = 'Radar\Adr\Fake\FakeMap::fakeMapMethod';
        $actual = $this->adr->fakeMapMethod();
        $this->assertSame($expect, $actual);
    }

    public function testRun()
    {
        FakeMiddleware::$count = 0;

        $this->adr->middle('Radar\Adr\Fake\FakeMiddleware');
        $this->adr->middle('Radar\Adr\Fake\FakeMiddleware');
        $this->adr->middle('Radar\Adr\Fake\FakeMiddleware');

        $response = $this->adr->run(
            ServerRequestFactory::fromGlobals(),
            new Response()
        );

        $actual = (string) $response->getBody();
        $this->assertSame('123456', $actual);
    }
}
