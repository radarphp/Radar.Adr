<?php
namespace Radar\Adr;

use Exception as AnyException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ExceptionHandler
{
    protected $request;
    protected $response;
    protected $exception;

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AnyException $exception
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
        $this->exec();
        return $this->response;
    }

    protected function exec()
    {
        $this->response = $this->response
            ->withStatus(500);

        $this->response->getBody()->write($this->exception->getMessage());
    }
}
