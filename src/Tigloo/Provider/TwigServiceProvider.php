<?php
declare(strict_types = 1);

namespace Tigloo\Provider;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;

final class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app): void
    {
        $app->set('path.twig', $app->get('path.resources').DIRECTORY_SEPARATOR.'views');
        
        $app->set('twig', function () use ($app) {
            $twig = $app->get('twig.environment');
            if ($app->has('environment')) {
                $twig->addGlobal('env', $app->get('environment'));
            }

            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->addExtension(new \Tigloo\View\TwigExtension($app));
            
            return $twig;
        });

        $app->set('twig.loader_array', function () {
            return new \Twig\Loader\ArrayLoader([]);
        });

        $app->set('twig.loader_system', function ($app) {
            $loader = new \Twig\Loader\FilesystemLoader();
            $loader->addPath($app->get('path.twig'));
            return $loader;
        });
        
        $app->set('twig.loader', function ($app) {
            return new \Twig\Loader\ChainLoader([
                $app->get('twig.loader_array'),
                $app->get('twig.loader_system')
            ]);
        });

        $app->set('twig.environment', function ($app) {
            return new \Twig\Environment(
                $app->get('twig.loader'),
                [
                    'charset' => $app->get('charset'),
                    'debug' => $app->get('debug'),
                    'strict_variables' => false
                ]
            );
        });
    }
}