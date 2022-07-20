<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Tigloo\Core\Contracts\EventDispatcherInterface;
use Tigloo\Core\Controller\ResolverController;
use Tigloo\Event\RequestEvent;

final class Runner
{
    protected EventDispatcherInterface $dispatcher;

    private ResolverController $resolver;

    public function __construct(EventDispatcherInterface $dispatcher, ResolverController $resolver)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
    }

    /**
     * Construit la réponse et la retourne
     * en activant les événements.
     * 
     * @param ServerRequestInterface $request
     * 
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $event = new RequestEvent($request);
            $this->dispatcher->dispatch($event);

            $controller = $this->resolver->getController($event->getRequest());
            $attributes = $this->resolver->getAttributes($event->getRequest(), $controller);

            $response = $controller($attributes);

            if (! $response instanceof ResponseInterface) {
                throw new RuntimeException('Not Implemented', 501);
            }
            
            $event = new ResponseEvent($request, $response);
            $this->dispatcher->dispatch($event);

            return $event->getResponse();

        } catch (\Exception $e) {
            
        }
    }
}