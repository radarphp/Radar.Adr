<?php
namespace Radar\Adr;

class BootTest extends \PHPUnit_Framework_TestCase
{
    protected $cache;

    protected function setUp()
    {
        $this->cache = __DIR__ . DIRECTORY_SEPARATOR . 'container.serialized';
        if (file_exists($this->cache)) {
            unlink($this->cache);
        }
    }

    protected function tearDown()
    {
        if (file_exists($this->cache)) {
            unlink($this->cache);
        }
    }

    public function testLoader()
    {
        $boot = new Boot([
            'filepath' => __DIR__ . DIRECTORY_SEPARATOR . '_env',
            'toEnv' => true,
        ]);

        $adr = $boot->adr();
        $this->assertInstanceOf('Radar\Adr\Adr', $adr);
        $this->assertSame('BAR', $_ENV['FOO']);
    }

    public function testCacheEnv()
    {
        $_ENV['RADAR_ADR_CONTAINER_CACHE'] = $this->cache;
        unset($_SERVER['RADAR_ADR_CONTAINER_CACHE']);
        putenv('RADAR_ADR_CONTAINER_CACHE=');
        $this->assertCache();
    }

    public function testCacheServer()
    {
        unset($_ENV['RADAR_ADR_CONTAINER_CACHE']);
        $_SERVER['RADAR_ADR_CONTAINER_CACHE'] = $this->cache;
        putenv('RADAR_ADR_CONTAINER_CACHE=');
        $this->assertCache();
    }

    public function testCachePutenv()
    {
        unset($_ENV['RADAR_ADR_CONTAINER_CACHE']);
        unset($_SERVER['RADAR_ADR_CONTAINER_CACHE']);
        putenv('RADAR_ADR_CONTAINER_CACHE=' . $this->cache);
        $this->assertCache();
    }

    protected function assertCache()
    {
        $this->assertFalse(file_exists($this->cache));
        $boot = new Boot(['filepath' => __DIR__ . DIRECTORY_SEPARATOR . '_env']);
        $adr = $boot->adr();
        $this->assertTrue(file_exists($this->cache));
        $this->assertInstanceOf('Radar\Adr\Adr', $adr);

        $adr = $boot->adr();
        $this->assertInstanceOf('Radar\Adr\Adr', $adr);
    }
}
