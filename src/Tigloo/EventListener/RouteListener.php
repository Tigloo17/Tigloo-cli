<?php
declare(strict_types = 1);

namespace Tigloo\EventListener;

use RuntimeException;
use Tigloo\Core\Contracts\EventSubscriberInterface;
use Tigloo\Event\RequestEvent;
use Tigloo\Routing\Router;

final class RouteListener implements EventSubscriberInterface
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function routeMatching(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $this->router->flush();
        $route = $this->router->match($request->getMethod(), $request->getUri());

        if (! $route) {
            throw new RuntimeException('Not Found', 404);
        }

        $request = $request->withAttribute('_route', $route);
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