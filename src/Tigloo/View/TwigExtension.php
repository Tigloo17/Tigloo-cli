<?php
declare(strict_types = 1);

namespace Tigloo\View;

use Tigloo\Container\Contracts\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private ContainerInterface $app;

    public function __construct(ContainerInterface $app) 
    {
        $this->app = $app;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('Route', [$this, 'generateUrlOfRoute']),
        ];
    }

    public function generateUrlOfRoute(string $name, array $attributes = [])
    {
        return $this->app->get('router')->generate($name, $attributes);
    }
}