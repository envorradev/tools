<?php declare(strict_types=1);

namespace Envorra\Tools\Flags;

use Envorra\Tools\Common\Contracts\Flags\Flag as FlagContract;

/**
 * ComparesFlags
 *
 * @package Envorra\Tools\Flags
 */
trait ComparesFlags
{
    /**
     * @inheritDoc
     */
    public function is(mixed $flag): bool
    {
        if($flag instanceof FlagContract) {
            $flag = $flag->value;
        }

        return ($flag & $this->value) > 0;
    }

    /**
     * @inheritDoc
     */
    public function isIn(array $flags): bool
    {
        foreach($flags as $flag) {
            if($this->is($flag)) {
                return true;
            }
        }
        return false;
    }
}
