<?php
declare(strict_types = 1);

namespace Tigloo\Event;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResponseEvent extends RequestEvent
{
    private ResponseInterface $response;

    public function __construct(ServerRequestInterface $request, ?ResponseInterface $response = null)
    {
        $this->response = $response;
        parent::__construct($request);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function handleResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

}