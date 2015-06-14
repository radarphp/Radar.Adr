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

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $action = $request->getAttribute('radar/adr:action');
        $request = $request->withoutAttribute('radar/adr:action');
        $response = $this->response($request, $response, $action);
        return $next($request, $response);
    }

    protected function response(Request $request, Response $response, Action $action)
    {
        $responder = $this->resolver->__invoke($action->getResponder());

        $domainSpec = $action->getDomain();
        if (! $domainSpec) {
            return $responder($request, $response);
        }

        $domain = $this->resolver->__invoke($domainSpec);
        $params = $this->params($request, $action);
        $result = call_user_func_array($domain, $params);
        return $responder($request, $response, $result);
    }

    protected function params(Request $request, Action $action)
    {
        $inputSpec = $action->getInput();
        if (! $inputSpec) {
            return [];
        }

        $input = $this->resolver->__invoke($inputSpec);
        return (array) $input($request);
    }
}
