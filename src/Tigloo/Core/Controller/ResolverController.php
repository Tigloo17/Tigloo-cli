<?php
declare(strict_types = 1);

namespace Tigloo\Core\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Reflector;
use RuntimeException;
use Tigloo\Container\Contracts\ContainerInterface;

class ResolverController
{
    private ContainerInterface $app;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function getController(ServerRequestInterface $request)
    {    
        if (! $route = $request->getAttribute('_route')) {
            throw new RuntimeException('Not Found', 404);
        }

        $action = $route->getAction();
        if (is_string($action)) {
            if (! strpos($action, '#')) {
                throw new RuntimeException('Bad Request', 400);
            }
            [$class, $method] = explode('#', $action);
            $controller = $this->controller(new \ReflectionMethod(new $class(), $method), $this->app);
        } elseif (is_object($action) && ! $action instanceof \Closure) {
            $controller = $this->controller((new \ReflectionObject($action))->getMethod('__invoke'), $this->app);
        } else {
            $controller = $this->controller(new \ReflectionFunction($action), $this->app);
        }

        return $controller;
    }

    public function getAttributes(ServerRequestInterface $request, object $controller)
    {

    }

    private function controller(Reflector $reflector, ContainerInterface $app): object
    {
        return new Class ($reflector, $app) {
            
            private Reflector $reflector;
            private ContainerInterface $app;
            
            public function __construct(Reflector $reflector, ContainerInterface $app)
            {
                $this->reflector = $reflector;
                $this->app = $app;               
            }

            public function getReflector(): Reflector
            {
                return $this->reflector;
            }

            public function __invoke(array $arguments = [])
            {
                if ($this->reflector->isClosure()) {
                    $controller = $this->reflector->invokeArgs($arguments);
                } else {
                    $class = $this->reflector->getDeclaringClass()->getName();
                    $instance = new $class();

                    if ($instance instanceof AbstractController) {
                        $instance = $instance->setContainer($this->app);
                    }

                    $controller = $this->reflector->invokeArgs($instance, $arguments);
                }    

                return $controller;
            }
        };
    }
}