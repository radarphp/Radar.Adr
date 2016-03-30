<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr;

use Aura\Di\ContainerBuilder;

/**
 *
 * Bootstraps the ADR instance with DI container configuration.
 *
 * @package radar/adr
 *
 */
class Boot
{
    protected $containerCache;

    public function __construct($containerCache = null)
    {
        $this->containerCache = $containerCache;
    }

    public function adr(array $config = [], $autoResolve = false)
    {
        if ($this->containerCache) {
            $di = $this->cachedContainer($config, $autoResolve);
        } else {
            $di = $this->newContainer($config, $autoResolve);
        }

        return $di->get('radar/adr:adr');
    }

    protected function cachedContainer(array $config, $autoResolve = false)
    {
        if (file_exists($this->containerCache)) {
            return unserialize(file_get_contents($this->containerCache));
        }

        $di = $this->newContainer($config, $autoResolve);
        file_put_contents($this->containerCache, serialize($di));
        return $di;
    }

    protected function newContainer(array $config, $autoResolve = false)
    {
        $config = array_merge(['Radar\Adr\Config'], $config);
        return (new ContainerBuilder())->newConfiguredInstance($config, $autoResolve);
    }
}
