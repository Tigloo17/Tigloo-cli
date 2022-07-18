<?php

namespace Tigloo\Core\Contracts;

interface EventSubscriberInterface
{
    /**
     * Retourne un tableau d'abonnement à 1 ou plusieurs événements et ajoute leurs récepteurs.
     * 
     * @return array <string <callable[]|callable priority>>
     */
    public function getSubscriberForEvent(): array;
}