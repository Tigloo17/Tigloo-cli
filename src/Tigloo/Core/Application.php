<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Container\Container;
use Tigloo\Core\Contracts\EventListenerProviderInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;


/**
 * [Description Application]
 */
class Application extends Container
{
    protected string $pathbase;

    protected array $providers = [];

    private bool $booted = false;
    
    public function __construct(string $pathbase)
    {
        $this->pathbase = $pathbase;
    }
    

    /**
     * Enregistre les services providers
     * 
     * @param ServiceProviderInterface $provider
     * 
     * @return void
     */
    public function register(ServiceProviderInterface $provider): void
    {
        $this->providers[] = $provider;
        parent::register($provider);
    }

    /**
     * Démarrer l'application.
     * 
     * Traite la demande et délivre une réponse
     * 
     * @return void
     */
    public function run(): void
    {
        if (! $this->booted) {
            $this->boot();
        }
    }

    /**
     * Démarre tous les providers.
     */
    public function boot(): void
    {
        $this->booted = true;
        
        foreach ($this->get('providers') as $provider) {
            if ($provider instanceof EventListenerProviderInterface) {
                $provider->subscribe($this, $this->get('event.dispatcher'));
            }
        }
    }

    public function emitter(): void
    {

    }
}