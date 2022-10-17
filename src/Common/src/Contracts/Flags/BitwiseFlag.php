<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Flags;

/**
 * BitwiseFlag
 *
 * @package Envorra\Tools\Contracts\Flags
 *
 * @extends Flag<int>
 */
interface BitwiseFlag extends Flag
{
    /**
     * @param  BitwiseFlag[]  $flags
     * @return BitwiseCombinedFlag
     */
    public function combine(array $flags): BitwiseCombinedFlag;
}
