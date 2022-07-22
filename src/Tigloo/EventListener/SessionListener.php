<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use Psr\Http\Message\ServerRequestInterface;
use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Core\Session;
use Tigloo\Event\RequestEvent;
use RuntimeException;

final class SessionListener implements EventSubscriberInterface
{
    private const KEY = '_csrf';
    
    private const LENGTH_TOKEN = 16;

    public function sessionStart(RequestEvent $event)
    {
        $session = $this->session = new Session();
        $request = $event->getRequest();
        
        if (! $session->isStarted()) {
            $session = $session::create($event->getRequest()->getUri()->getScheme());
            if (! $session->start()) {
                throw new RuntimeException('Internal Server Error', 500);
            }
        }
        
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            
            $body = $request->getParsedBody();
            $name = $body['csrf_name'] ?? null;
            $value = $body['csrf_value'] ?? null;
            
            if ($name === null || $value === null || $this->validateToken($name, $value) === false) {
                $request = $this->generateToken($request);
                throw new RuntimeException('Failed CSRF check!', 400);
            }
        }

        $request = $this->generateToken($request);
        $event->handleRequest($request);
    }

    private function generateToken(ServerRequestInterface $request): ServerRequestInterface
    {
        $name = uniqid(self::KEY);
        $value = bin2hex(random_bytes(self::LENGTH_TOKEN));
        $this->session->set(self::KEY, [$name => $value]);
        $request = $request->withAttribute('csrf_name', $name);
        $request = $request->withAttribute('csrf_value', $value);
        
        return $request;
    }

    private function validateToken(?string $name, ?string $value): bool
    {
        $pairKey = $this->session->get(self::KEY);
        if (! isset($pairKey[$name])) {
            return false;
        }
        $token = $pairKey[$name];
        return hash_equals($token, $value);
    }

    public function getSubscriberForEvent(): array
    {
        return [
            RequestEvent::class => [
                [[$this, 'sessionStart'], 100],
            ]
        ];
    }
}