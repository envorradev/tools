<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Arrays;

/**
 * ConvertsToArray
 *
 * @package  Envorra\Tools\Common\Contracts\Arrays
 *
 * @template TKey of array-key
 * @template TValue
 */
interface ConvertsToArray
{
    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array;

    /**
     * @return ArrayDataObject<TKey, TValue>
     */
    public function toArrayObject(): ArrayDataObject;
}
