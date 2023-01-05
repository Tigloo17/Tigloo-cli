<?php
declare(strict_types = 1);

namespace Tigloo\Provider;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\EventDispatcherInterface;
use Tigloo\Core\Contracts\EventListenerProviderInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Tigloo\Core\EventDispatcher;
use Tigloo\EventListener\{CookieListener, ResponseListener, SessionListener, RouteListener, ErrorListener};
use Tigloo\Core\Runner;
use Tigloo\Routing\Router;
use Tigloo\Core\Controller\ResolverController;
use Tigloo\Core\Cookies\CookieCollection;
use GuzzleHttp\Psr7\ServerRequest;

final class HttpServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(ContainerInterface $app): void
    {
        $app->set('event.dispatcher', function () {
            return new EventDispatcher();
        });

        $app->set('kernel', function ($app) {
            return new Runner(
                $app->get('event.dispatcher'), 
                $app->get('controller.resolver')
            );
        });

        $app->set('controller.resolver', function ($app) {
            return new ResolverController($app);
        });

        $app->set('request.factory', $app->factory(function () {
            return ServerRequest::fromGlobals();
        }));

        $app->set('request', function ($app) {
            return $app->get('request.factory');
        });

        $app->set('router', function ($app) {
            return new Router(
                $app->getRoutes(),
                $app->get('event.dispatcher')
            );
        });

        $app->set('cookies.factory', $app->factory(function ($app) {
            return new CookieCollection();
        }));

        $app->set('cookies', function ($app) {
            return $app->get('cookies.factory');
        });
    }

    public function subscriber(ContainerInterface $app, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->addSubscriber(new SessionListener($app->get('environment')));
        $dispatcher->addSubscriber(new RouteListener($app->get('router')));
        $dispatcher->addSubscriber(new ResponseListener($app->get('charset')));
        $dispatcher->addSubscriber(new ErrorListener($app->get('twig'), $app->get('debug')));
    }
}