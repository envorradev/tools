<?php declare(strict_types=1);

namespace Envorra\Tools\Filesystem;

use SplFileInfo;
use BadMethodCallException;
use RecursiveDirectoryIterator;
use Envorra\Tools\Common\Contracts\Filesystem\PathObject;
use Envorra\Tools\Common\Contracts\Filesystem\FileObject;

/**
 * Path
 *
 * @package Envorra\Tools\Filesystem
 *
 * @implements PathObject
 *
 * @mixin SplFileInfo
 */
class Path implements PathObject
{
    public readonly string $enclosingFolder;
    public readonly string $name;

    public readonly string $realPath;

    public readonly array $segments;
    protected readonly SplFileInfo $info;

    /**
     * @inheritDoc
     */
    public function __construct(
        SplFileInfo|string|null $path = null,
        PathObject|SplFileInfo|string|null $basePath = null
    ) {
        if (!$path) {
            $path = __DIR__;
        }

        if (is_string($path)) {
            if ($basePath) {
                $path = self::concat($basePath, $path);
            }

            $path = new SplFileInfo($path);
        }

        $this->info = $path;
        $this->name = $this->info->getBasename();
        $this->realPath = $this->info->getRealPath() ?: '';
        $this->segments = explode(DIRECTORY_SEPARATOR, $this->realPath);
        $this->enclosingFolder = array_slice($this->segments, -2, 1)[0] ?? '';
    }

    /**
     * @inheritDoc
     */
    public static function concat(FileObject|PathObject|SplFileInfo|string|array ...$paths): string
    {
        if (is_array($paths[0])) {
            $paths = $paths[0];
        }

        $final = '';
        foreach ($paths as $path) {
            $path = self::normalize((string) $path);

            $path = str_ends_with($path, DIRECTORY_SEPARATOR) ? substr($path, 0, -1) : $path;

            if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
                $path = realpath($path) ? $path : substr($path, 1);
            }

            $final .= empty($final) ? $path : DIRECTORY_SEPARATOR.$path;
        }
        return $final;
    }

    /**
     * @inheritDoc
     */
    public static function normalize(string $path): string
    {
        return preg_replace('~[\\\\/]+~', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists($this->info, $method)) {
            return $this->info->$method(...$arguments);
        }

        throw new BadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function children(): array
    {
        return array_values(
            array_map(
                callback: function (SplFileInfo $item) {
                    if ($item->isFile()) {
                        return new File($item);
                    }

                    if ($item->isDir()) {
                        return new Path($item);
                    }

                    return null;
                },
                array: array_filter(
                    array: iterator_to_array($this->iterator()),
                    callback: fn(SplFileInfo $item) => $item->getFilename() !== '.' && $item->getFilename() !== '..'
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function dirs(): array
    {
        return array_values(
            array_filter(
                array: $this->children(),
                callback: fn($item) => $item instanceof Path
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function files(): array
    {
        return array_values(
            array_filter(
                array: $this->children(),
                callback: fn($item) => $item instanceof File
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function filesByExtension(string $extension): array
    {
        if (str_starts_with($extension, '.')) {
            $extension = substr($extension, 1);
        }

        return $this->filterFiles(fn(File $file) => $file->extension === $extension);
    }

    /**
     * @inheritDoc
     */
    public function filesByName(string $name): array
    {
        return $this->filterFiles(fn(File $file) => $file->name === $name);
    }

    /**
     * @inheritDoc
     */
    public function filterDirs(callable $filter): array
    {
        return array_values(array_filter($this->dirs(), $filter));
    }

    /**
     * @inheritDoc
     */
    public function filterFiles(callable $filter): array
    {
        return array_values(array_filter($this->files(), $filter));
    }

    /**
     * @inheritDoc
     */
    public function getDir(string $name): ?Path
    {
        return $this->filterDirs(fn(Path $path) => $path->name === $name)[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getFile(string $fileName): ?File
    {
        return $this->filterFiles(fn(File $file) => $file->fileName === $fileName)[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasChildren(): bool
    {
        return count($this->children()) > 0;
    }

    /**
     * @inheritDoc
     */
    public function hasDir(string $name): bool
    {
        return $this->getDir($name) !== null;
    }

    /**
     * @inheritDoc
     */
    public function hasDirs(): bool
    {
        return count($this->dirs()) > 0;
    }

    /**
     * @inheritDoc
     */
    public function hasFile(string $fileName): bool
    {
        return $this->getFile($fileName) !== null;
    }

    /**
     * @inheritDoc
     */
    public function hasFiles(): bool
    {
        return count($this->files()) > 0;
    }

    /**
     * @inheritDoc
     */
    public function info(): SplFileInfo
    {
        return $this->info;
    }

    /**
     * @inheritDoc
     */
    public function iterator(): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator($this->realPath);
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->realPath;
    }

    /**
     * @inheritDoc
     */
    public function up(int $numberOfJumps = 1): Path
    {
        return new Path(
            path: implode(
                separator: DIRECTORY_SEPARATOR,
                array: array_slice(
                    array: $this->segments,
                    offset: 0,
                    length: -1 * abs($numberOfJumps)
                )
            )
        );
    }


}
