<?php
declare(strict_types = 1);

namespace Tigloo\Container;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Tigloo\Container\Exceptions\{NotFoundException, IdentifierException};
use SplObjectStorage;
use ReturnTypeWillChange;

/**
 * Class Principal du container
 * 
 * Container Suivant la standardise PSR11
 * 
 * @author Collard Jean-Michel <collajm@hotmail.com>
 * @since 1.0-dev
 */
class Container implements ContainerInterface
{
    /**
     * @var SplObjectStorage
     */
    private  $factories;

    /**
     * @var array
     */
    private array $aliases = [];

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->factories = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function __get(string $id): mixed
    {
        return $this->offsetGet($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return array_keys($this->aliases);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id, mixed $default = null): mixed
    {
        if ($this->offsetExists($id)) {
            return $this->offsetGet($id);
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $id, mixed $value): void
    {
        $this->offsetSet($id, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return $this->offsetExists($id);
    }

    /**
     * {@inheritdoc}
     */
    public function factory(object $object): object
    {
        if (! $object instanceof \Closure || ! method_exists($object, '__invoke')) {
            throw new IdentifierException('Le service défini "%s", n\'est pas un service invokable');
        }

        $this->factories->attach($object);
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function register(ServiceProviderInterface $provider): void
    {
        $provider->register($this);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->getAliases());
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $id): bool
    {
        return isset($this->aliases[$id]) && false != $this->aliases[$id];
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $id): mixed
    {
        if (! $this->offsetExists($id)) {
            throw new NotFoundException($id);
        }

        if (! is_object($this->instances[$id]) || ! method_exists($this->instances[$id], '__invoke')) {
            return $this->instances[$id];
        }

        if (isset($this->factories[$this->instances[$id]])) {
            return $this->instances[$id]($this);
        }

        $value = $this->instances[$id] = $this->instances[$id]($this);
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $id, mixed $value): void
    {
        $this->aliases[$id] = true;
        $this->instances[$id] = $value;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $id): void
    {
        if ($this->offsetExists($id)) {
            unset($this->instances[$id]);
            $this->aliases[$id] = false;
        }
    }
}