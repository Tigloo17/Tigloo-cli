<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Event\RequestEvent;

final class SessionListener implements EventSubscriberInterface
{
    public function sessionStart(RequestEvent $event): void
    {
        
    }

    public function getSubscriberForEvent(): array
    {
        return [
            RequestEvent::class => [
                [[$this, 'sessionStart'], 100]
            ]
        ];
    }
}