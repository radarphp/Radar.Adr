<?php
namespace Radar\Adr\Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Resolver;
use Radar\Adr\Router\Route;

class ActionHandler
{
    protected $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $route = $request->getAttribute('radar/adr:route');
        $request = $request->withoutAttribute('radar/adr:route');
        $response = $this->response($route, $request, $response);
        return $next($request, $response);
    }

    protected function response(
        Route $route,
        Request $request,
        Response $response
    ) {
        $responder = $this->resolver->__invoke($route->responder);

        if ($route->domain) {
            $payload = $this->payload($route, $request);
            return $responder($request, $response, $payload);
        }

        return $responder($request, $response);
    }

    protected function payload(Route $route, Request $request)
    {
        $domain = $this->resolver->__invoke($route->domain);

        $input = [];
        if ($route->input) {
            $input = $this->resolver->__invoke($route->input);
            $input = (array) $input($request);
        }

        return call_user_func_array($domain, $input);
    }
}
