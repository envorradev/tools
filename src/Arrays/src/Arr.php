<?php declare(strict_types=1);

namespace Envorra\Tools\Arrays;

use Traversable;
use ArrayIterator;
use Envorra\Tools\Common\ToolResolver;
use Envorra\Tools\Common\Contracts\Json\JsonDataObject;
use Envorra\Tools\Common\Contracts\Arrays\ArrayDataObject;
use Envorra\Tools\Common\Exceptions\ToolResolutionException;

/**
 * Arr
 *
 * @package  Envorra\Tools\Arrays
 *
 * @template TKey of array-key
 * @template TValue
 */
class Arr implements ArrayDataObject
{
    /**
     * @var array
     */
    protected readonly array $original;

    /**
     * @var ToolResolver
     */
    protected ToolResolver $resolver;

    /**
     * @param  array  $items
     */
    public function __construct(protected array $items = [])
    {
        $this->original = $this->items;
        $this->resolver = new ToolResolver();
    }

    /**
     * @inheritDoc
     */
    public static function make(array $array): static
    {
        return new self($array);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function append(mixed $value): static
    {
        $this->push($value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @inheritDoc
     */
    public function delete(mixed $value): static
    {
        if ($this->has($value)) {
            $this->remove($this->search($value));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $callback): static
    {
        $this->items = array_filter($this->items, $callback);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function first(): mixed
    {
        return $this->nth(1);
    }

    /**
     * @inheritDoc
     */
    public function get(int|string $key): mixed
    {
        if ($this->hasKey($key)) {
            return $this->items[$key];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function getOriginal(): mixed
    {
        return $this->original;
    }

    /**
     * @inheritDoc
     */
    public function has(mixed $value): bool
    {
        return in_array($value, $this->values());
    }

    /**
     * @inheritDoc
     */
    public function hasChanged(): bool
    {
        return $this->items === $this->original;
    }

    /**
     * @inheritDoc
     */
    public function hasKey(int|string $key): bool
    {
        return in_array($key, $this->keys());
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * @inheritDoc
     */
    public function last(): mixed
    {
        return $this->nth(-1);
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callback): static
    {
        $this->items = array_map($callback, $this->items);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function nth(int $nth): mixed
    {
        if ($nth > 0) {
            $nth--;
        }

        if ($nth < $this->count() && $nth >= 0) {
            $counter = 0;
            foreach ($this->items as $item) {
                if ($counter === $nth) {
                    return $item;
                }
                $counter++;
            }
        }

        if ($nth < 0) {
            return (clone $this)->reverse()->nth(abs($nth));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->hasKey($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function prepend(mixed $value): static
    {
        $this->reverse()->push($value)->reverse();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function push(mixed $value): static
    {
        $this->items[] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(int|string $key): static
    {
        if ($this->hasKey($key)) {
            unset($this->items[$key]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reset(): static
    {
        $this->items = $this->original;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reverse(): static
    {
        $this->items = array_reverse($this->items);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function search(mixed $value): mixed
    {
        if ($this->has($value)) {
            return array_search($value, $this->items);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function set(int|string $key, mixed $value): static
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toJson(): string
    {
        try {
            return $this->toJsonObject()->minify()->getJson();
        } catch (ToolResolutionException) {
            return json_encode($this->items);
        }
    }


    /**
     * @inheritDoc
     * @throws ToolResolutionException
     */
    public function toJsonObject(): JsonDataObject
    {
        return $this->resolver->resolve(JsonDataObject::class)::make($this->items);
    }

    /**
     * @inheritDoc
     */
    public function toPrettyJson(): string
    {
        try {
            return $this->toJsonObject()->pretty()->getJson();
        } catch (ToolResolutionException) {
            return json_encode($this->items, JSON_PRETTY_PRINT);
        }
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->toJson();
    }

    /**
     * @inheritDoc
     */
    public function values(): array
    {
        return array_values($this->items);
    }
}
