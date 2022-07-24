<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Event\ErrorsEvent;
use GuzzleHttp\Psr7\{Response, Utils, MimeType};
use Tigloo\Core\JsonResponse;
use Twig\Environment;
use Throwable;

class ErrorListener implements EventSubscriberInterface
{
    private Environment $view;

    private bool $debug = false;

    public function __construct(Environment $twig, bool $debug = false)
    {
        $this->view = $twig;
        $this->debug = $debug;
    }

    public function errorHandle(ErrorsEvent $event)
    {
        $throwable = $event->getThrowable();
        $response = $event->getResponse();

        $response = (null === $response) 
            ? new Response($throwable->getCode()) 
            : $response->withStatus($throwable->getCode());
        
        if (in_array($event->getRequest()->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $json = $this->throwableEncode($this->throwableJson($throwable));
            $response = $response->withHeader('Content-Type', MimeType::fromExtension('json'));
            $response = $response->withBody(Utils::streamFor($json));
        
        } else {
            if ($this->debug) {
                // système d'erreur pour débugger rapidement.
                $response = $response->withHeader('Content-Type', MimeType::fromExtension('json'));
                $json = $this->throwableEncode($this->throwableJson($throwable));
                $response = $response->withBody(Utils::streamFor($json));
            } 
        }

        $event->handleResponse($response);
    }

    private function throwableJson(Throwable $throwable): array
    {
        $json               = [];
        $json['message']    = $throwable->getMessage();
        $json['code']       = $throwable->getCode();
        $json['file']       = $throwable->getFile();
        $json['line']       = $throwable->getLine();
        $json['trace']      = $throwable->getTraceAsString();
        return $json;
    }

    private function throwableEncode(array $throwable): string
    {
        return json_encode($throwable, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function getSubscriberForEvent(): array
    {
        return [
            ErrorsEvent::class => [
                [[$this, 'errorHandle'], 0]
            ]
        ];
    }
}