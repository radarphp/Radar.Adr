<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware;

/**
 *
 *
 * @package   Radar\Middleware
 */
class ActionSpecsFactory
{
    /**
     * Returns a new ActionSpecs instance.
     *
     * @param string $handler
     * @return ActionSpecs
     */
    public function newInstance(string $handler): ActionSpecs
    {
        return new ActionSpecs($handler);
    }
}
