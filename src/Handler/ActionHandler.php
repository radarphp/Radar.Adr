<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware\Handler;

use Arbiter\ActionHandler as Arbiter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

/**
 *
 * Dispatches to the Action stored in the `radar/adr:action` Request attribute.
 *
 * @package radar/middleware
 *
 */
class ActionHandler extends Arbiter implements MiddlewareInterface
{
    /**
     *
     * Dispatches to the Action stored in the `radar/adr:action` Request
     * attribute.
     *
     * @param ServerRequestInterface $request The HTTP request object.
     *
     * @param RequestHandlerInterface $handler The handler middleware decorator.
     *
     * @return ResponseInterface
     *
     * @throws \InvalidArgumentException
     * @throws \Arbiter\Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $action = $request->getAttribute('radar/adr:action');
        $request = $request->withoutAttribute('radar/adr:action');
        return $this->handle($action, $request, new Response());
    }
}
