<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Maps;

use Countable;
use ArrayAccess;
use JsonSerializable;
use IteratorAggregate;

/**
 * Map
 *
 * @package  Envorra\Tools\Contracts\Maps
 *
 * @template TKey of array-key
 * @template TValue
 */
interface Map extends ArrayAccess, IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @param  mixed  $item
     * @return bool
     */
    public function exists(mixed $item): bool;

    /**
     * @param  mixed  $item
     * @return TValue|null
     */
    public function find(mixed $item): mixed;

    /**
     * @param  TValue  $item
     * @return TKey|null
     */
    public function getKey(mixed $item): mixed;

    /**
     * @param  TKey  $key
     * @return bool
     */
    public function keyExists(string|int $key): bool;

    /**
     * @return array<TKey, TValue>
     */
    public function map(): array;
}
