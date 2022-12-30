<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Core\Cookies\CookieCollection;
use Tigloo\Event\RequestEvent;

final class CookieListener implements EventSubscriberInterface
{
    protected $cookies;

    public function __construct(CookieCollection $cookies)
    {
        $this->cookies = $cookies;
    }

    public function addToCollection(RequestEvent $event)
    {
        $request = $event->getRequest();
        foreach($request->getCookieParams() as $name => $value) { // [name => value]
            // ... créer un new cookie() => avec les information
        }
    }

    public function getSubscriberForEvent(): array
    {
        return [
            RequestEvent::class => [
                [[$this, 'addToCollection'], 100],
            ]
        ];
    }
}