<?php
declare(strict_types = 1);

namespace Tigloo\Core\Cookies;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;
use Traversable;

final class CookieCollection implements IteratorAggregate, Countable
{
    private array $cookies = [];

    public function __construct(array $cookies = [])
    {
        foreach ($cookies as $cookie) {
            if (! $cookie instanceof Cookie) {
                throw new RuntimeException('La collection de cookies ne prend en compte que des instances de Cookie', 500);
            }
            $this->cookies[$cookie->getName()] = $cookie;
        }
    }

    public function add(Cookie $cookie): self
    {
        $new = clone $this;
        $new[$cookie->getName()] = $cookie;
        return $new;
    }

    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    public function get(string $name): ?Cookie
    {
        return $this->cookies[$name] ?? null;
    }

    public function getKeys(): array
    {
        return array_keys($this->cookies);
    }

    public function remove(string $name): ?Cookie
    {
        if (! $this->has($name)) {
            return null;
        }
        $removed = $this->cookies[$name];
        unset($this->cookies[$name]);
        return $removed;
    }

    public function count(): int
    {
        return count($this->cookies);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->cookies);
    }
}