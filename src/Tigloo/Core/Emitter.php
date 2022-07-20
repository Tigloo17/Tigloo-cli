<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Psr\Http\Message\ResponseInterface;

class Emitter
{
    public function emit(ResponseInterface $response): void
    {
        # code...
    }
}