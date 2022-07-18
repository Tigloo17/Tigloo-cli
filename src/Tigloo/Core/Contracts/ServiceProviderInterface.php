<?php

namespace Tigloo\Core\Contracts;

use Tigloo\Container\Contracts\ContainerInterface;

interface ServiceProviderInterface
{
    /**
     * Interface enregistreur d'éléments fournis, par les providers, dans le container.
     * 
     * @param ContainerInterface $container
     * 
     * @return void
     */
    public function register(ContainerInterface $container): void;
}