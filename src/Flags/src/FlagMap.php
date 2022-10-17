<?php declare(strict_types=1);

namespace Envorra\Tools\Flags;

use ReflectionClass;
use ReflectionException;
use Envorra\Tools\Common\Contracts\Maps\Map;
use Envorra\Tools\Common\Traits\HandlesMapContract;

/**
 * FlagMap
 *
 * @package Envorra\Tools\Flags
 */
class FlagMap implements Map
{
    use HandlesMapContract;

    public function __construct(protected array $map = [])
    {

    }

    /**
     * @inheritDoc
     */
    public function map(): array
    {
        return $this->map;
    }

    public static function fromClass(object|string $class): self
    {
        $flags = [];

        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException) {
            return new self();
        }

        foreach($reflection->getReflectionConstants() as $constant) {
            $name = $constant->getName();
            $flags[$name] = new Flag($constant->getValue(), $name, $reflection->getName());
        }

        return new self($flags);
    }
}
