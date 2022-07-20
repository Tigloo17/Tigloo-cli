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

    protected string $charset;

    protected array $providers = [];

    private bool $booted = false;
    
    public function __construct(string $pathbase, string $charset = 'UTF-8')
    {
        $this->pathbase = $pathbase;
        $this->charset = $charset;

        $this->registerContainer();
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
     * Traite la demande et délivre une réponse
     * 
     * @return void
     */
    public function run(): void
    {
        if (! $this->booted) {
            $this->boot();
        }

        $response = $this->get('http.kernel')->handle($this->get('request'));
        $emitter = new Emitter();
        $emitter->emit($response);
    }

    /**
     * Démarre tous les providers.
     */
    public function boot(): void
    {
        $this->booted = true;
        
        foreach ($this->providers as $provider) {
            if ($provider instanceof EventListenerProviderInterface) {
                $provider->subscriber($this, $this->get('event.dispatcher'));
            }
        }
    }

    /**
     * Enregistre les fichiers de configuration dans le container
     * 
     * @return void
     */
    private function registerContainer(): void
    {
        $this->set('path.base', rtrim($this->pathbase, '\/'));
        $this->set('path.config', $this->get('path.base').DIRECTORY_SEPARATOR.'config');
        $this->set('path.public', $this->get('path.base').DIRECTORY_SEPARATOR.'public');
        $this->set('path.app', $this->get('path.base').DIRECTORY_SEPARATOR.'app');
        $this->set('path.resources', $this->get('path.base').DIRECTORY_SEPARATOR.'resources');
        $this->set('charset', $this->charset);

        $configuration = (new FileSystem())->load($this->get('path.config'))->output();
        if (! $configuration->isEmpty()) {
            $iterator = $configuration->getIterator();
            $iterator->rewind();
            
            while ($iterator->valid()) {
                $this->set($iterator->key(), $iterator->current());
                $iterator->next();
            }
        }

        if ($this->has('providers')) {
            foreach ($this->get('providers') as $provider) {
                if (in_array(ServiceProviderInterface::class, class_implements($provider))) {
                    $this->register(new $provider());
                }
            }
        }
    }
}