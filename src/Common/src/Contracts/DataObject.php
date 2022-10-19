<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts;

/**
 * DataObject
 *
 * @package  Envorra\Tools\Common\Contracts
 *
 * @template T
 */
interface DataObject
{
    /**
     * @return T
     */
    public function getOriginal(): mixed;

    /**
     * @return bool
     */
    public function hasChanged(): bool;

    /**
     * @return static<T>
     */
    public function reset(): static;
}
