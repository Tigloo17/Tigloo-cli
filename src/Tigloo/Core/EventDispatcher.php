<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Psr\EventDispatcher\StoppableEventInterface;
use Tigloo\Core\Contracts\EventDispatcherInterface;
use Tigloo\Core\Contracts\EventSubscriberInterface;

class EventDispatcher implements EventDispatcherInterface
{

    /**
     * Stock tous les émetteurs avec leurs écouteurs.
     * 
     * @var array
     */
    private array $listeners = [];

    /**
     * Stock tous les émetteurs et leurs récepteurs, trié par priorité.
     * 
     * @var array
     */
    private array $sorted = [];

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event): void
    {
        foreach ($this->getListenersForEvent($event) as $listener) {
            if (
                in_array(StoppableEventInterface::class, class_implements($event))
                && $event->isPropagationStopped()
            ) {
                break;
            }

            $listener($event);
        }
    }

    /**
     * Php version >= 8.0
     * 
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if (isset($this->listeners[$event::class])) {

        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners(string $namespace): array
    {
        return $this->listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener(string $namespace, callable $listener, int $priority = 0): void
    {
        $this->listeners[$namespace][$priority][] = $listener;
        unset($this->sorted[$namespace]);   
    }

    /**
     * {@inheritdoc}
     * $namespace => [[[], $priority], [], []];
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscriberForEvent() as $namespace => $listener) { 
            if (! is_array($listener[0])) {
                $this->addListener($namespace, $listener[0], $listener[1]);
            } else {
                foreach ($listener as $listen) {
                    $this->addListener($namespace, $listen[0], $listen[1]);
                }
            }
        }
    }
}