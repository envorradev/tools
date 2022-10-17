<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Arrays;

use Countable;
use ArrayAccess;
use IteratorAggregate;

/**
 * AccessibleAsArray
 *
 * @package Envorra\Tools\Common\Contracts\Arrays
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @extends ArrayAccess<TKey, TValue>
 * @extends IteratorAggregate<TKey, TValue>
 */
interface AccessibleAsArray extends ArrayAccess, IteratorAggregate, Countable
{
    /**
     * All Items
     *
     * @return array<TKey, TValue>
     */
    public function all(): array;

    /**
     * @param  TKey|string|int  $key
     * @return TValue|null
     */
    public function get(string|int $key): mixed;
}
