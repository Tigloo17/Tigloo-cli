<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Event\ResponseEvent;

class ResponseListener implements EventSubscriberInterface
{
    public function onResponse(ResponseEvent $event)
    {

        // reprendre les headers de la requetes...
        $response = $event->getResponse();
        // headers...
        $event->handleResponse($response);
    }

    public function getSubscriberForEvent(): array
    {
        return [
            ResponseEvent::class => [
                [[$this, 'onResponse'], -100]
            ]
        ];
    }
}