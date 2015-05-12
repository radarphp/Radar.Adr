<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use josegonzalez\Dotenv\Loader;

class Boot
{
    protected $envPath;

    public function __construct($envPath)
    {
        $this->envPath = $envPath;
    }

    public function __invoke(array $config = [])
    {
        $loader = new Loader($this->envPath);
        $loader->parse();
        $loader->toEnv();

        if (! empty($_ENV['RADAR_ADR_CONTAINER_CACHE'])) {
            $di = $this->cachedContainer($config);
        } else {
            $di = $this->newContainer($config);
        }

        return $di->get('radar/adr:adr');
    }

    protected function cachedContainer(array $config)
    {
        $file = $_ENV['RADAR_ADR_CONTAINER_CACHE'];
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }

        $di = $this->newContainer($config);
        file_put_contents($file, serialize($di));
        return $di;
    }

    protected function newContainer(array $config)
    {
        $config = array_merge(['Radar\Adr\Config'], $config);
        return (new ContainerBuilder())->newConfiguredInstance([], $config);
    }
}
