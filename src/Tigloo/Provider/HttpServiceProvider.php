<?php
declare(strict_types = 1);

namespace Tigloo\Provider;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\EventDispatcherInterface;
use Tigloo\Core\Contracts\EventListenerProviderInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Tigloo\Core\EventDispatcher;
use Tigloo\Core\Runner;
use Tigloo\Core\Controller\ResolverController;
use GuzzleHttp\Psr7\ServerRequest;
use Tigloo\EventListener\SessionListener;

final class HttpServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->set('event.dispatcher', function () {
            return new EventDispatcher();
        });

        $container->set('http.kernel', function ($container) {
            return new Runner(
                $container->get('event.dispatcher'), 
                $container->get('controller.resolver')
            );
        });

        $container->set('controller.resolver', function ($container) {
            return new ResolverController($container);
        });

        $container->set('request.factory', $container->factory(function () {
            return ServerRequest::fromGlobals();
        }));

        $container->set('request', function ($container) {
            return $container->get('request.factory');
        });
    }

    public function subscriber(ContainerInterface $app, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->addSubscriber(new SessionListener());
    } 
}