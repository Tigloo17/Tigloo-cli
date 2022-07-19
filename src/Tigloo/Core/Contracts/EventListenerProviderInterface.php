<?php
declare(strict_types = 1);

namespace Tigloo\Core\Contracts;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\EventDispatcherInterface;

interface EventListenerProviderInterface
{
    /**
     * interface pour les fournisseurs d'événements.
     * 
     * @param ContainerInterface $app
     * @param EventDispatcherInterface $dispatcher
     * 
     * @return void
     */
    public function subscriber(ContainerInterface $app, EventDispatcherInterface $dispatcher): void;
}