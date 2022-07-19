<?php
declare(strict_types = 1);

namespace Tigloo\Collection\Contracts;

use ArrayAccess;
use ArrayIterator;
use Countable;

interface CollectionInterface extends Countable, ArrayAccess
{
    public function add(string|array $name, mixed $value): void;

    public function get(string $name, mixed $default = null): mixed;

    public function keys(): CollectionInterface;

    public function values(): CollectionInterface;

    public function all(): array;

    public function merge(CollectionInterface $collection): CollectionInterface;

    public function isEmpty(): bool;

    public function has(string $name): bool;
    
    public function getIterator(): ArrayIterator;
}
