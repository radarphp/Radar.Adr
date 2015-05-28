<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use josegonzalez\Dotenv\Loader;

class Boot
{
    protected $dotenv = [
        'toEnv' => true,
        'putenv' => true,
    ];

    public function __construct(array $dotenv = [])
    {
        $this->dotenv = array_merge($this->dotenv, $dotenv);
    }

    public function adr(array $config = [])
    {
        Loader::load($this->dotenv);

        $cache = $this->getCache();
        if ($cache) {
            $di = $this->cachedContainer($config, $cache);
        } else {
            $di = $this->newContainer($config);
        }

        return $di->get('radar/adr:adr');
    }

    protected function getCache()
    {
        if (isset($_ENV['RADAR_ADR_CONTAINER_CACHE'])) {
            return $_ENV['RADAR_ADR_CONTAINER_CACHE'];
        }

        $cache = getenv('RADAR_ADR_CONTAINER_CACHE');
        if ($cache) {
            return $cache;
        }

        if (isset($_SERVER['RADAR_ADR_CONTAINER_CACHE'])) {
            return $_SERVER['RADAR_ADR_CONTAINER_CACHE'];
        }
    }

    protected function cachedContainer(array $config, $cache)
    {
        if (file_exists($cache)) {
            return unserialize(file_get_contents($cache));
        }

        $di = $this->newContainer($config);
        file_put_contents($cache, serialize($di));
        return $di;
    }

    protected function newContainer(array $config)
    {
        $config = array_merge(['Radar\Adr\Config'], $config);
        return (new ContainerBuilder())->newConfiguredInstance([], $config);
    }
}
