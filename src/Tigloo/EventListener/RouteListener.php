<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Event\RequestEvent;

final class RouteListener implements EventSubscriberInterface
{
    public function routeMatching(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $event->handleRequest($request);
    }

    public function getSubscriberForEvent(): array
    {
        return [
            RequestEvent::class => [
                [[$this, 'routeMatching'], 50]
            ]
        ];
    }
}