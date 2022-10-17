<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Arrays;

use Envorra\Tools\Common\Contracts\DataObject;
use Envorra\Tools\Common\Contracts\Json\ConvertsToJson;
use Envorra\Tools\Common\Contracts\Strings\ConvertsToString;

/**
 * ArrayDataObject
 *
 * @package  Envorra\Tools\Common\Contracts\Arrays
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @extends DataObject<array<TKey, TValue>>
 * @extends AccessibleAsArray<TKey, TValue>
 */
interface ArrayDataObject extends DataObject, AccessibleAsArray, ConvertsToJson, ConvertsToString
{
    /**
     * @param  array<TKey, TValue>  $array
     * @return static
     */
    public static function make(array $array): static;

    /**
     * @param  TValue  $value
     * @return static
     */
    public function append(mixed $value): static;

    /**
     * @param  TValue  $value
     * @return static
     */
    public function delete(mixed $value): static;

    /**
     * @param  callable  $callback
     * @return static
     */
    public function filter(callable $callback): static;

    /**
     * @return TValue
     */
    public function first(): mixed;

    /**
     * @param  TValue  $value
     * @return bool
     */
    public function has(mixed $value): bool;

    /**
     * @param  TKey  $key
     * @return bool
     */
    public function hasKey(string|int $key): bool;

    /**
     * @return TKey[]
     */
    public function keys(): array;

    /**
     * @return TValue
     */
    public function last(): mixed;

    /**
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback): static;

    /**
     * @param  int  $nth
     * @return TValue
     */
    public function nth(int $nth): mixed;

    /**
     * @param  TValue  $value
     * @return static
     */
    public function prepend(mixed $value): static;

    /**
     * @param  TValue  $value
     * @return static
     */
    public function push(mixed $value): static;

    /**
     * @param  TKey  $key
     * @return static
     */
    public function remove(string|int $key): static;

    /**
     * @return static
     */
    public function reverse(): static;

    /**
     * @param  TValue  $value
     * @return TKey|null
     */
    public function search(mixed $value): mixed;

    /**
     * @param  TKey    $key
     * @param  TValue  $value
     * @return static
     */
    public function set(string|int $key, mixed $value): static;

    /**
     * @return TValue[]
     */
    public function values(): array;
}
