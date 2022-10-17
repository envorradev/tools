<?php declare(strict_types=1);

namespace Envorra\Tools\Flags;

use Envorra\Tools\Common\Contracts\Flags\BitwiseCombinedFlag;
use Envorra\Tools\Common\Contracts\Flags\Flag as FlagContract;

/**
 * CombinedFlag
 *
 * @package Envorra\Tools\Flags
 */
class CombinedFlag implements BitwiseCombinedFlag
{
    use ComparesFlags;

    public function __construct(
        public readonly int $value,
        public readonly array $names = [],
        public readonly ?string $class = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function combine(array $flags): BitwiseCombinedFlag
    {
        $value = $this->value;
        $names = $this->names;

        foreach($flags as $flag) {
            if($flag instanceof self) {
                $names = array_merge($names, $flag->names);
            }

            if($flag instanceof Flag) {
                if($flag->name) {
                    $names[] = $flag->name;
                }
            }

            if($flag instanceof FlagContract) {
                $flag = $flag->value;
            }

            $value |= $flag;
        }

        return new self($value, $names, $this->class);
    }
}
