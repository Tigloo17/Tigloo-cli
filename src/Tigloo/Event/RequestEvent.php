<?php
declare(strict_types = 1);

namespace Tigloo\Event;

use Psr\Http\Message\ServerRequestInterface;

final class RequestEvent
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function handleRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}