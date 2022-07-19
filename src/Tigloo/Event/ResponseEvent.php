<?php
declare(strict_types = 1);

namespace Tigloo\Event;

use Psr\Http\Message\ResponseInterface;

class ResponseEvent extends RequestEvent
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
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