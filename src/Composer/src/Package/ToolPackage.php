<?php declare(strict_types=1);

namespace Envorra\Tools\Composer\Package;

use Envorra\Tools\Filesystem\File;
use Envorra\Tools\Composer\Exceptions\UnexpectedFileException;

/**
 * ToolPackage
 *
 * @package Envorra\Tools\Composer
 */
class ToolPackage extends AbstractComposerPackage
{
    public readonly array $resolves;

    /**
     * @param  File  $file
     * @throws UnexpectedFileException
     */
    public function __construct(File $file)
    {
        parent::__construct($file);

        if($this->extra && isset($this->extra['tool-resolver'])) {
            $relative = $this->extra['tool-resolver']['relative'] ?? true;
            $map = $this->extra['tool-resolver']['resolves'] ?? [];

            $this->resolves = array_combine(
                keys: array_map(
                    callback: fn($class) => $relative
                        ? $this->namespaceItem(
                            item: $class,
                            namespace: $this->packageFinder->tool('common')->namespaces()[0])
                        : $class,
                    array: array_keys($map)
                ),
                values: array_map(
                    callback: fn($class) => $relative
                        ? $this->namespaceItem(
                            item: $class,
                            namespace: $this->namespaces()[0])
                        : $class,
                    array: array_values($map)
                )
            );
        } else {
            $this->resolves = [];
        }
    }

    /**
     * @param  string  $item
     * @param  string  $namespace
     * @return string
     */
    protected function namespaceItem(string $item, string $namespace): string
    {
        $namespace = str_ends_with($namespace, '\\') ? $namespace : $namespace.'\\';
        $item = str_starts_with($item, '\\') ? substr($item, 1) : $item;
        return str_starts_with($item, $namespace) ? $item : $namespace.$item;
    }
}
