<?php
declare(strict_types = 1);

namespace Tigloo\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Tigloo\Routing\Contracts\RouteInterface;
use RuntimeException;
use Tigloo\Core\Contracts\EventDispatcherInterface;

final class Router
{
    private RouteInterface $routes;
    private EventDispatcherInterface $dispatcher;
    private array $names = [];
    private string $regexRoute = '`\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';
    private array $regex = [
        'num' => '[0-9]++',
        'alpha' => '[a-zA-Z\-]++',
        'alphanum' => '[0-9A-Za-z\-]++',
        '*' => '.+?',
        '' => '[^/\.]++'
    ];

    public function __construct(RouteInterface $routes, EventDispatcherInterface $dispatcher)
    {
        $this->routes = $routes;
        $this->dispatcher = $dispatcher;
    }

    public function flush(): void
    {
        foreach ($this->routes->getRouteForMatching() as $route) {
            $name = $route->getName();
            if (! $name) {
                $name = $route->getMethod().'_'.$route->getPattern();
                $name = str_replace(['/', ':', '|', '-'], '_', $name);
                $name = preg_replace('#\[.*\]#', '$1', $name);
                $name = rtrim(preg_replace('/_+/', '_', $name), '_');
                $route->withName($name);
            }

            if (isset($this->names[$name])) {
                throw new RuntimeException(sprintf('Impossible de redéclarer la route %s', $name), 500);
            }

            $this->names[$name] = $route->getPattern();
        }
    }

    public function match(string $method, ServerRequestInterface &$request)
    {
        $params = [];
        $path = rtrim($request->getUri()->getPath(), '/');
        $path = empty($path) ? '/' : $path;
        
        foreach ($this->routes->getRouteForMatching() as $route) {    
            if (! (stripos($route->getMethod(), $method) !== false)) {
               continue;
            }

            if ($route->getPattern() === '*') {
                $matched = true;
            } elseif (($pos = strpos($route->getPattern(), '[')) === false) {
                $matched = strcmp($path, $route->getPattern()) === 0;
            } else {
                if (strncmp($path, $route->getPattern(), $pos) !== 0) {
                    continue;
                }
                $regex = $this->compile($route->getPattern());
                $matched = preg_match($regex, $path, $params) === 1;
            }

            if ($matched) {
                if ($params) {
                    
                    foreach ($params as $key => $value) {
                        if (is_numeric($key)) {
                            unset($params[$key]);
                            continue;
                        }
                        
                        $request = $request->withAttribute($key, $value);
                    }
                }
                // middleware Event...
                if ($route->hasEvent()) {
                    foreach ($route->getEvent() as $event) {
                        $this->dispatcher->dispatch(new $event($request));
                    }
                }
                return $route;
            }
        }
        return null;
    }

    public function generate(string $name, array $parameters = [])
    {
        if (! isset($this->names[$name])) {
            throw new RuntimeException(sprintf('La route %s n\'existe pas!', $name), 500);
        }

        $route = $this->names[$name];
        if (preg_match_all($this->regexRoute, $route, $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                list($block, $type, $param, $optional) = $match;
                if (isset($parameters[$param])) {
                    $route = str_replace($block, (array_key_exists($param, $parameters)) ? (string) $parameters[$param] : '', $route);
                } else {
                    $route = str_replace($block, '', $route);
                } 
            }
        }
        return $route;
    }

    private function compile($route)
    {
        if (preg_match_all($this->regexRoute, $route, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list($block, $type, $name, $optional) = $match;
                
                $optional = $optional !== '' ? '?' : null;
                if (isset($this->regex[$type])) {
                    $type = $this->regex[$type];
                }
                
                $pattern = sprintf(
                    '%3$s(?:(%1$s%2$s)%3$s)%3$s',
                    ($name !== '' ? "?P<$name>" : null),
                    $type,
                    $optional
                );
                
                $route = str_replace($block, $pattern, $route);
            }
        }
        return "`^$route$`u";
    }
}