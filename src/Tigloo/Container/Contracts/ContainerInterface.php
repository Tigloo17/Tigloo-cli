<?php

namespace Tigloo\Container\Contracts;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Countable;
use ArrayAccess;

interface ContainerInterface extends PsrContainerInterface, Countable, ArrayAccess
{
    /**
     * Retourne tous les aliases enregistrer dans le container.
     * 
     * @return array
     */
    public function getAliases(): array;

    /**
     * @param string $id
     * @param mixed $value
     * 
     * @return void
     */
    public function set(string $id, mixed $value): void;

    /**
     * @param ServiceProviderInterface $provider
     * 
     * @return void
     */
    public function register(ServiceProviderInterface $provider): void;

    /**
     * @param object $object
     * 
     * @return object
     */
    public function factory(object $object): object;
}