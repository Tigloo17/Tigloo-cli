<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Psr\Http\Message\ResponseInterface;
use Tigloo\Core\Cookies\Cookie;
use Tigloo\Core\Cookies\CookieCollection;

class Emitter
{
    public function emit(ResponseInterface $response, CookieCollection $cookies): void
    {
        $this->emitStatus($response);
        $this->emitHeaders($response);
        $this->emitCookies($cookies);
        $this->emitBody($response);

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $headers) {
            $first = true;
            foreach ($headers as $header) {
                header(sprintf(
                    '%s:%s',
                    $name,
                    $header
                ), $first);
                $first = false;
            }
        }
    }

    private function emitStatus(ResponseInterface $response): void
    {
        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase() ?? ''
        ), true, $response->getStatusCode()); // if code 300 ??
    }

    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();
        if (! $body->isSeekable()) {
            echo $body;
            return;
        }

        $body->rewind();
        while(! $body->eof()) {
            echo $body->read(8192);
        }
    }

    private function emitCookies(CookieCollection $cookies): void
    {
        foreach ($cookies as $cookie) {
            if ($cookie instanceof Cookie) {
                setcookie($cookie->getName(), $cookie->getValue(), $cookie->getOptions());
            }
        }
    }
}