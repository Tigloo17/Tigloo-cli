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
            new TwigFunction('CsrfValue', [$this, 'csrfGenerator'])
        ];
    }

    public function generateUrlOfRoute(string $name, array $attributes = [])
    {
        return $this->app->get('router')->generate($name, $attributes);
    }

    public function csrfGenerator(): string
    {
        $session = new Session();
        if ($session->has('csrf_value')) {
            return $session->get('_csrf')[array_key_first($session->get('_csrf'))];
        }
    }
}