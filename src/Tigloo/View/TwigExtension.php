<?php
declare(strict_types = 1);

namespace Tigloo\View;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Session;
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
            new TwigFunction('Csrf', [$this, 'csrfGenerator'])
        ];
    }

    public function generateUrlOfRoute(string $name, array $attributes = [])
    {
        return $this->app->get('router')->generate($name, $attributes);
    }

    public function csrfGenerator(): string
    {
        $session = new Session();
        return '<input type="hidden" name="csrf" value="'.$session->get('csrf_token').'">';
    }
}