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

    private $env;

    public function __construct($env)
    {
        $this->env = $env;
    }

    public function sessionStart(RequestEvent $event)
    {
        if (! isset($this->env->CSRF_KEY)) {
            throw new RuntimeException('CSRF KEY not valid', 500);
        }

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
            
            if ($this->validateReferer($request) === false && ($value === null || $this->validateToken($value) === false)) {
                throw new RuntimeException('Failed CSRF check!', 400);
            }
            
        } else {
            $request = $this->generateToken($request);
        }
        
        $event->handleRequest($request);
    }

    private function generateToken(ServerRequestInterface $request): ServerRequestInterface
    {
        $value = bin2hex(random_bytes(self::LENGTH_TOKEN));
        $this->session->set(self::KEY, [$this->env->CSRF_KEY => $value]);
        $request = $request->withAttribute('csrf_token', $value);
        
        return $request;
    }

    private function validateToken(?string $value): bool
    {
        $pairKey = $this->session->get(self::KEY);
        if (! isset($pairKey[$this->env->CSRF_KEY])) {
            return false;
        }
        $token = $pairKey[$this->env->CSRF_KEY];
        return hash_equals($token, $value);
    }

    private function validateReferer($request): bool
    {
        preg_match(
            '/^(?:https?:\/\/)?(?:[^@\/\n]+@)?(?:www\.)?([^:\/?\n]+)/', 
            $request->getServerParams()['HTTP_REFERER'], 
            $matched
        );

        return $matched[1] ? (rtrim($this->env->APP_URL, '/') == $matched[1]) : false;
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