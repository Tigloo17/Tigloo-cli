<?php
declare(strict_types = 1);

namespace Tigloo\Provider;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\EventDispatcherInterface;
use Tigloo\Core\Contracts\EventListenerProviderInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Tigloo\Core\EventDispatcher;
use Tigloo\Core\Runner;

final class HttpServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->set('event.dispatcher', function ($container) {
            return new EventDispatcher();
        });

        $container->set('http.kernel', function ($container) {
            return new Runner($container->get('event.dispatcher'));
        });
    }

    public function subscriber(ContainerInterface $app, EventDispatcherInterface $dispatcher): void
    {
        
    } 
}