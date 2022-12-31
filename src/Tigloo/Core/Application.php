<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Container\Container;
use Tigloo\Core\Contracts\EventListenerProviderInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Tigloo\Routing\Contracts\RouteInterface;

/**
 * [Description Application]
 */
class Application extends Container
{
    protected string $pathbase;

    protected string $charset = 'UTF-8';

    protected bool $debug = false;

    protected array $providers = [];

    private bool $booted = false;
    
    public function __construct(string $pathbase, bool $debug = false)
    {
        parent::__construct();

        $this->pathbase = $pathbase;
        $this->debug = $debug;
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
        
        $response = $this->get('kernel')->handle($this->get('request'));
        $emitter = new Emitter();
        $emitter->emit($response, $this->get('cookies'));
    }

    public function getRoutes(): ?RouteInterface
    {
        $file = $this->get('path.app').DIRECTORY_SEPARATOR.'routes.php';
        if (file_exists($file)) {
            if (@require $file) {
                if (isset($route) && $route instanceof RouteInterface) {
                    return $route;
                }
            }
        }

        return null;
    }

    /**
     * Démarre tous les providers.
     */
    private function boot(): void
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
        $this->set('path.environment', $this->get('path.base').DIRECTORY_SEPARATOR.'.env');
        $this->set('charset', $this->charset);
        $this->set('debug', $this->debug);
        $this->set('environment', (new FileSystem())->load($this->get('path.environment'))->output());
        
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