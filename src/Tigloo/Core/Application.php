<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Tigloo\Container\Container;
use Tigloo\Core\Contracts\ServiceProviderInterface;

class Application extends Container
{
    public function __construct(string $pathBase)
    {
        
    }

    public function register(ServiceProviderInterface $provider): void
    {
        $this->providers[] = $provider;
        parent::register($provider);
    }
}