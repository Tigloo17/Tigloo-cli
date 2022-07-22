<?php
declare(strict_types = 1);

namespace Tigloo\Routing;

use Psr\Http\Message\UriInterface;
use Tigloo\Routing\Contracts\RouteInterface;

final class Router
{
    private RouteInterface $routes;

    public function __construct(RouteInterface $routes)
    {
        $this->routes = $routes;
    }

    public function flush(): void
    {
        
    }

    public function match(string $method, UriInterface $uri)
    {

    }

    public function generate(string $name, array $parameters = [])
    {

    }

    private function compile($route)
    {

    }
}