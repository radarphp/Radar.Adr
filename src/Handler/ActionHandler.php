<?php
namespace Radar\Adr\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Radar\Adr\Action;
use Radar\Adr\Resolver;
use Radar\Adr\Route;

class ActionHandler
{
    protected $resolver;

    public function __construct(callable $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $action = $request->getAttribute('radar/adr:action');
        $request = $request->withoutAttribute('radar/adr:action');
        $response = $this->handle($action, $request, $response);
        return $next($request, $response);
    }

    protected function handle(Action $action, Request $request, Response $response)
    {
        $responder = $this->resolve($action->getResponder());

        $domainSpec = $action->getDomain();
        if (! $domainSpec) {
            return $responder($request, $response);
        }

        $domain = $this->resolve($domainSpec);
        $params = $this->params($action, $request);
        $payload = call_user_func_array($domain, $params);
        return $responder($request, $response, $payload);
    }

    protected function params(Action $action, Request $request)
    {
        $inputSpec = $action->getInput();
        if (! $inputSpec) {
            return [];
        }

        $input = $this->resolve($inputSpec);
        return (array) $input($request);
    }

    protected function resolve($spec)
    {
        return call_user_func($this->resolver, $spec);
    }
}
