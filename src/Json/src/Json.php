<?php declare(strict_types=1);

namespace Envorra\Tools\Json;

use stdClass;
use Traversable;
use ArrayIterator;
use Envorra\Tools\Common\ToolResolver;
use Envorra\Tools\Common\Contracts\Json\JsonDataObject;
use Envorra\Tools\Common\Contracts\Arrays\ArrayDataObject;
use Envorra\Tools\Common\Contracts\Arrays\AccessibleAsArray;
use Envorra\Tools\Common\Exceptions\ToolResolutionException;

/**
 * Json
 *
 * @package Envorra\Tools\Json
 *
 * @implements AccessibleAsArray<array-key, mixed>
 */
class Json implements JsonDataObject, AccessibleAsArray
{
    protected array $data;
    protected int $encodeFlags = 0;
    protected string $json;
    protected readonly string $original;
    protected ToolResolver $resolver;

    /**
     * @param  string|null  $json
     * @param  int          $defaultEncodeFlags
     */
    public function __construct(
        string|null $json = null,
        protected readonly int $defaultEncodeFlags = 0,
    ) {
        $this->encodeFlags = $this->defaultEncodeFlags;
        $this->original = self::isValid($json) ? json_encode(json_decode($json), $this->encodeFlags) : '[]';
        $this->json = $this->original;
        $this->data = $this->toArray();
        $this->resolver = new ToolResolver();
    }

    /**
     * @inheritDoc
     */
    public static function isValid(mixed $json): bool
    {
        if ($json) {
            json_decode($json);
            return json_last_error() === JSON_ERROR_NONE;
        }
        return false;
    }

    /**
     * @inheritDoc
     * @throws ToolResolutionException
     */
    public function toArrayObject(): ArrayDataObject
    {
        return $this->resolver->resolve(ArrayDataObject::class)::make($this->data);
    }

    /**
     * @inheritDoc
     */
    public static function make(mixed $data): static
    {
        return new self(JsonTool::encode($data));
    }

    /**
     * @param  string  $property
     * @return mixed
     */
    public function __get(string $property): mixed
    {
        if ($this->keyExists($property)) {
            return $this->toObject()->$property;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function addEncodeFlag(int $flag): static
    {
        $this->encodeFlags |= $flag;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @inheritDoc
     */
    public function get(string|int|null $key = null): mixed
    {
        if ($key) {
            if ($this->offsetExists($key)) {
                return $this->offsetGet($key);
            }

            return $this->dottedGet(explode('.', $key), $this->data);
        }
        return $this->all();
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @inheritDoc
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @inheritDoc
     */
    public function getOriginal(): string
    {
        return $this->original;
    }

    /**
     * @inheritDoc
     */
    public function hasChanged(): bool
    {
        return $this->original !== $this->json;
    }

    /**
     * @param  string  $key
     * @return bool
     */
    public function keyExists(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /**
     * @inheritDoc
     */
    public function minify(): static
    {
        $this->removeEncodeFlags();
        $this->refreshJson();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        if (is_string($offset)) {
            return isset($this->data[$offset]);
        }

        if (is_int($offset)) {
            return $offset < $this->count();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        if ($this->offsetExists($offset)) {
            if (is_string($offset)) {
                return $this->data[$offset];
            }

            if (is_int($offset)) {
                $counter = 0;
                foreach ($this->data as $item) {
                    if ($counter === $offset) {
                        return $item;
                    }
                    $counter++;
                }
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
        $this->refreshJson();
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset) && $this->offsetExists($offset)) {
            unset($this->data[$offset]);
            $this->refreshJson();
        }
    }

    /**
     * @inheritDoc
     */
    public function pretty(): static
    {
        $this->addEncodeFlag(JSON_PRETTY_PRINT);
        $this->refreshJson();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeEncodeFlags(): static
    {
        $this->encodeFlags = 0;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reset(): static
    {
        $this->json = $this->original;
        $this->data = $this->toArray();
        $this->encodeFlags = $this->defaultEncodeFlags;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return (array) json_decode($this->json, true);
    }

    /**
     * @inheritDoc
     */
    public function toObject(): stdClass
    {
        return (object) json_decode($this->json, false);
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->json;
    }

    /**
     * @param  array  $keys
     * @param  array  $data
     * @return mixed
     */
    protected function dottedGet(array $keys, array $data): mixed
    {
        if (count($keys)) {
            if (array_key_exists($keys[0], $data)) {
                if (count($keys) === 1) {
                    return $data[$keys[0]];
                }

                if (is_array($data[$keys[0]])) {
                    return $this->dottedGet(array_slice($keys, 1), $data[$keys[0]]);
                }
            }
        }
        return null;
    }

    /**
     * @return void
     */
    protected function refreshJson(): void
    {
        $this->json = json_encode($this->data, $this->encodeFlags);
    }
}
