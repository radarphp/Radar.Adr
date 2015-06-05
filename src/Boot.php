<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use josegonzalez\Dotenv\Loader;

class Boot
{
    protected $containerCache;

    public function __construct($containerCache = null)
    {
        $this->containerCache = $containerCache;
    }

    public function adr(array $config = [])
    {
        if ($this->containerCache) {
            $di = $this->cachedContainer($config);
        } else {
            $di = $this->newContainer($config);
        }

        return $di->get('radar/adr:adr');
    }

    protected function cachedContainer(array $config)
    {
        if (file_exists($this->containerCache)) {
            return unserialize(file_get_contents($this->containerCache));
        }

        $di = $this->newContainer($config);
        file_put_contents($this->containerCache, serialize($di));
        return $di;
    }

    protected function newContainer(array $config)
    {
        $config = array_merge(['Radar\Adr\Config'], $config);
        return (new ContainerBuilder())->newConfiguredInstance($config);
    }
}
