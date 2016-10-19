<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use Aura\Router\Rule\RuleIterator;
use Radar\Adr\Fake\FakeMiddleware;
use Radar\Adr\Route;
use Telegraph\TelegraphFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

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
        $this->telegraph = new TelegraphFactory();

        $this->adr = new Adr(
            $this->fakeMap,
            $this->fakeRules,
            $this->telegraph,
            $resolver
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
        $this->adr->middle('Radar\Adr\Fake\FakeResponseMiddleware');

        $response = $this->adr->run(
            ServerRequestFactory::fromGlobals()
        );

        $actual = (string) $response->getBody();
        $this->assertSame('123', $actual);
    }
}
