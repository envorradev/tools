<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Flags;

/**
 * Flag
 *
 * @package  Envorra\Tools\Contracts\Flags
 *
 * @template TValue
 *
 * @property-read TValue $value
 */
interface Flag
{
    /**
     * @param  Flag|TValue  $flag
     * @return bool
     */
    public function is(mixed $flag): bool;

    /**
     * @param  Flag[]|TValue[]  $flags
     * @return bool
     */
    public function isIn(array $flags): bool;
}
