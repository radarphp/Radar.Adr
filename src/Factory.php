<?php
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;
use josegonzalez\Dotenv\Loader;

// rename to Bootstrap or Boot (again) and make Factory work like Dispatcher::factory
class Factory
{
    protected $envPath;

    public function __construct($envPath)
    {
        $this->envPath = $envPath;
    }

    public function newInstance(array $config = [])
    {
        $loader = new Loader($this->envPath);
        $loader->parse();
        $loader->toEnv();

        $config = array_merge(['Radar\Adr\Config'], $config);
        $di = (new ContainerBuilder())->newConfiguredInstance([], $config);

        return $di->newInstance('Radar\Adr\Adr');
    }
}
