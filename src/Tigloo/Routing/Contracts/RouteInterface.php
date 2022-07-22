<?php
declare(strict_types = 1);

namespace Tigloo\Routing\Contracts;

use Tigloo\Routing\Route;

interface RouteInterface
{
    public function get(string $pattern, object|string $action): Route;
    
    public function post(string $pattern, object|string $action): Route;
    
    public function put(string $pattern, object|string $action): Route;
    
    public function options(string $pattern, object|string $action): Route;
    
    public function delete(string $pattern, object|string $action): Route;
    
    public function patch(string $pattern, object|string $action): Route;

    public function getRouteForMatching(): array;
}