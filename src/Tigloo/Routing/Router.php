<?php
declare(strict_types = 1);

namespace Tigloo\Routing;

use Psr\Http\Message\UriInterface;

final class Router
{
    private RouteCollection $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
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