<?php
declare(strict_types = 1);

namespace Tigloo\Core\Contracts;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

interface EventDispatcherInterface extends PsrEventDispatcherInterface, ListenerProviderInterface
{
    /**
     * Retourne un tableau de tous les listeners trié par priorité.
     * 
     * @param string $eventClass
     * 
     * @return array
     */
    public function getListeners(string $namespace): array;

    /**
     * Ajoute un récepteur d'événement qui écoute un émetteur spécifique.
     * 
     * @param string $namespace Namespace de l'émetteur d'événement. [Event::class]
     * @param callable $listener
     * @param int $priority de -100 à +100 par défault 0
     * 
     * @return void
     */
    public function addListener(string $namespace, callable $listener, int $priority = 0): void;

    /**
     * Ajoute un ou plusieurs évenements accompagné de leurs récepteurs.
     * 
     * @param EventSubscriberInterface $subscriber
     * 
     * @return void
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void;
}