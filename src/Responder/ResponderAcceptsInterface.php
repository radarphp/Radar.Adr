<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr\Responder;

/**
 *
 * Indicates that the Responder will examine the `Accepts` Request header.
 *
 * @package radar/adr
 *
 */
interface ResponderAcceptsInterface
{
    public static function accepts();
}
