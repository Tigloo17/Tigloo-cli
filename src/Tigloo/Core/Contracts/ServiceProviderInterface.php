<?php

namespace Tigloo\Core\Contracts;

use Tigloo\Container\Contracts\ContainerInterface;

interface ServiceProviderInterface
{
    /**
     * Enregistre les éléments des providers dans le container.
     * 
     * @param ContainerInterface $container
     * 
     * @return void
     */
    public function register(ContainerInterface $container): void;
}