<?php
/**
 *
 * This file is part of Radar for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Radar\Middleware\Handler;

use Arbiter\ActionFactory;
use Arbiter\ActionHandler as Arbiter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Radar\Middleware\ActionSpecsFactory;
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
     * @var string Attribute name for handler reference
     */
    private $handlerAttribute = 'request-handler';
    /**
     * @var ActionSpecsFactory
     */
    private $actionSpecsFactory;
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * ActionHandler constructor.
     *
     * @param ActionSpecsFactory $actionSpecsFactory
     * @param ActionFactory $actionFactory
     * @param callable|null $resolver
     */
    public function __construct(
        ActionSpecsFactory $actionSpecsFactory,
        ActionFactory $actionFactory,
        callable $resolver = null
    )
    {
        parent::__construct($resolver);
        $this->actionSpecsFactory = $actionSpecsFactory;
        $this->actionFactory = $actionFactory;
    }

    /**
     * Set the attribute name to store handler reference.
     *
     * @param string $handlerAttribute
     * @return ActionHandler
     */
    public function handlerAttribute(string $handlerAttribute): self
    {
        $this->handlerAttribute = $handlerAttribute;
        return $this;
    }

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
        $requestHandler = $request->getAttribute($this->handlerAttribute);
        $request = $request->withoutAttribute($this->handlerAttribute);

        $specs = $this->actionSpecsFactory->newInstance($requestHandler);

        $action = $this->actionFactory->newInstance(
            $specs->input,
            $specs->domain,
            $specs->responder
        );

        return $this->handle($action, $request, new Response());
    }
}
