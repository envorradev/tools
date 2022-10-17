<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Traits;

use Traversable;
use ArrayIterator;

/**
 * HandlesMapContract
 *
 * @package Envorra\Tools\Traits
 */
trait HandlesMapContract
{
    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->map());
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->map()[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->map()[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->map()[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->map()[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->map());
    }

    /**
     * @inheritDoc
     */
    abstract public function map(): array;

    /**
     * @inheritDoc
     */
    public function find(mixed $item): mixed
    {
        if($key = $this->getKey($item)) {
            return $this->map()[$key];
        }

        if($this->keyExists($item)) {
            return $this->map()[$item];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function exists(mixed $item): bool
    {
        return $this->find($item) !== null;
    }

    /**
     * @inheritDoc
     */
    public function keyExists(int|string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @inheritDoc
     */
    public function getKey(mixed $item): mixed
    {
        if($this->keyExists($item)) {
            return array_search($item, $this->map());
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->map();
    }
}
