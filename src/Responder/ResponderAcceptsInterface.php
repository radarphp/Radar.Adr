<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware\Responder;

/**
 *
 * Indicates that the Responder will examine the `Accepts` Request header.
 *
 * @package radar/middleware
 *
 */
interface ResponderAcceptsInterface
{
    /**
     *
     * Returns the list of media types the Responder can generate.
     *
     * @return array
     *
     */
    public static function accepts();
}
