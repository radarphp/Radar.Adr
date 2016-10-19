<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Adr\Responder;

use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 * An Abstract Responder.
 *
 * @package radar/adr
 *
 */
abstract class AbstractResponder
{
    /**
     *
     * The HTTP response.
     *
     * @var Response
     *
     */
    protected $response;

    /**
     * __construct
     *
     * @param Response $response PSR7 Response
     *
     * @access public
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }
}
