<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Strings;

use Stringable;

/**
 * ConvertsToString
 *
 * @package Envorra\Tools\Common\Contracts\Strings
 */
interface ConvertsToString extends Stringable
{
    /**
     * @return string
     */
    public function toString(): string;
}
