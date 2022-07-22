<?php
declare(strict_types = 1);

namespace Tigloo\Routing;

use Tigloo\Routing\Contracts\RouteInterface;

final class RouteCollection implements RouteInterface
{
    private array $routes = [];

    public function get(string $pattern, object|string $action): Route
    {
        return $this->addRoute('GET', $pattern, $action);
    }

    public function post(string $pattern, object|string $action): Route
    {
        return $this->addRoute('POST', $pattern, $action);
    }

    public function put(string $pattern, object|string $action): Route
    {
        return $this->addRoute('PUT', $pattern, $action);
    }

    public function delete(string $pattern, object|string $action): Route
    {
        return $this->addRoute('DELETE', $pattern, $action);
    }

    public function options(string $pattern, object|string $action): Route
    {
        return $this->addRoute('OPTIONS', $pattern, $action);
    }

    public function patch(string $pattern, object|string $action): Route
    {
        return $this->addRoute('PATCH', $pattern, $action);
    }

    public function getRouteForMatching(): array
    {
        return $this->routes;
    }

    protected function addRoute(string $method, string $pattern, object|string $action): Route
    {
        $this->routes[] = $route = new Route($method, $pattern, $action);
        return $route;
    }
}