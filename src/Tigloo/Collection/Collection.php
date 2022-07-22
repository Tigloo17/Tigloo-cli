<?php
declare(strict_types = 1);

namespace Tigloo\Collection;

use Tigloo\Collection\Contracts\CollectionInterface;
use ArrayIterator;
use ReturnTypeWillChange;

final class Collection implements CollectionInterface
{
    private array $collection = [];

    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    public function add(string|array $name, mixed $value = null): void
    {
        if (is_string($name)) {
            $this->offsetSet($name, $value);
        } else {
            if (is_array($name)) {
                foreach ($name as $key => $value) {
                    if (is_int($key)) {
                        $this->collection[] = $value;
                    } else {
                        $this->collection[$key] = $value;
                    }
                }
            }
        } 
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        }

        return $default;
    }

    public function all(): array
    {
        return $this->collection;
    }

    public function merge(CollectionInterface $collection): CollectionInterface
    {
        return new static(array_merge($this->collection, $collection->all()));
    }

    public function keys(): CollectionInterface
    {
        return new static(array_keys($this->collection));
    }

    public function values(): CollectionInterface
    {
        return new static(array_values($this->collection));
    }

    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    public function has(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->collection);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $name): bool
    {
        return isset($this->collection[$name]);   
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $name): mixed
    {
        return $this->collection[$name];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $name, mixed $value): void
    {
        $this->collection[$name] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $name): void
    {
        if ($this->offsetExists($name)) {
            unset($this->collection[$name]);
        }
    }

    public function __set($name, $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function __get($name): mixed
    {
        return $this->offsetGet($name);
    }
}