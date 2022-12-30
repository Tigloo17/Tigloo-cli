<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use GuzzleHttp\Psr7\MimeType;
use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Event\ResponseEvent;

class ResponseListener implements EventSubscriberInterface
{
    private string $charset;

    public function __construct(string $charset = 'UTF-8')
    {
        $this->charset = $charset;
    }

    public function onResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        
        if (! $event->getRequest()->hasHeader('Content-Language')) {
            $response = $response->withHeader('Content-Language', 'fr-FR');
        }

        if (! $response->hasHeader('Content-Type')) {
            $mimeType = MimeType::fromExtension('htm').'; charset='.$this->charset;
            $response = $response->withHeader('Content-Type', $mimeType);
        }
        
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