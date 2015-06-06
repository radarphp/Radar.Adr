<?php
namespace Radar\Adr\Handler;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ExceptionHandler
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $response = $next($request, $response);
        } catch (Exception $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write($e->getMessage());
        }
        return $response;
    }
}
