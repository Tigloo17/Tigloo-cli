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

    private ?string $csrf_key = null;

    public function __construct(?string $csrf_key)
    {
        $this->csrf_key = $csrf_key;
    }

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
            $value = $body['csrf_token'] ?? null;
            
            if ($value === null || $this->validateToken($value) === false) {
                $request = $this->generateToken($request);
                throw new RuntimeException('Failed CSRF check!', 400);
            }
        }

        $request = $this->generateToken($request);
        $event->handleRequest($request);
    }

    private function generateToken(ServerRequestInterface $request): ServerRequestInterface
    {
        $value = bin2hex(random_bytes(self::LENGTH_TOKEN));
        $this->session->set(self::KEY, [$this->csrf_key => $value]);
        $request = $request->withAttribute('csrf_token', $value);
        
        return $request;
    }

    private function validateToken(?string $value): bool
    {
        $pairKey = $this->session->get(self::KEY);
        if (! isset($pairKey[$this->csrf_key])) {
            return false;
        }
        $token = $pairKey[$this->csrf_key];
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