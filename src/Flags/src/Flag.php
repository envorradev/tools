<?php declare(strict_types=1);

namespace Envorra\Tools\Flags;

use Envorra\Tools\Common\Contracts\Flags\BitwiseFlag;
use Envorra\Tools\Common\Contracts\Flags\Flag as FlagContract;
use Envorra\Tools\Common\Contracts\Flags\BitwiseCombinedFlag;

/**
 * Flag
 *
 * @package Envorra\Tools\Flags
 */
class Flag implements BitwiseFlag
{
    use ComparesFlags;

    public function __construct(
        public readonly int $value,
        public readonly ?string $name = null,
        public readonly ?string $class = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function combine(array $flags): BitwiseCombinedFlag
    {
        $value = $this->value;
        $names = $this->name ? [$this->name] : [];

        foreach ($flags as $flag) {
            if ($flag instanceof self) {
                if ($flag->name) {
                    $names[] = $flag->name;
                }
            }

            if ($flag instanceof FlagContract) {
                $flag = $flag->value;
            }

            $value |= $flag;
        }

        return new CombinedFlag($value, $names, $this->class);
    }


}
