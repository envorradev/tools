<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Filesystem;

use SplFileInfo;
use RecursiveDirectoryIterator;
use Envorra\Tools\Common\Contracts\Strings\ConvertsToString;

/**
 * PathObject
 *
 * @package Envorra\Tools\Common\Contracts\Filesystem
 *
 * @extends ConvertsToString
 *
 * @mixin SplFileInfo
 */
interface PathObject extends ConvertsToString
{
    /**
     * @param  SplFileInfo|string|null             $pathInfo
     * @param  PathObject|SplFileInfo|string|null  $basePath
     */
    public function __construct(
        SplFileInfo|string|null $pathInfo = null,
        PathObject|SplFileInfo|string|null $basePath = null
    );

    /**
     * @param  FileObject|PathObject|SplFileInfo|string|array  ...$paths
     * @return string
     */
    public static function concat(FileObject|PathObject|SplFileInfo|string|array ...$paths): string;

    /**
     * @param  string  $path
     * @return string
     */
    public static function normalize(string $path): string;

    /**
     * @param  string  $method
     * @param  array   $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments): mixed;

    /**
     * @return array
     */
    public function children(): array;

    /**
     * @return array
     */
    public function dirs(): array;

    /**
     * @return array
     */
    public function files(): array;

    /**
     * @param  string  $extension
     * @return array
     */
    public function filesByExtension(string $extension): array;

    /**
     * @param  string  $name
     * @return array
     */
    public function filesByName(string $name): array;

    /**
     * @param  callable  $filter
     * @return array
     */
    public function filterDirs(callable $filter): array;

    /**
     * @param  callable  $filter
     * @return array
     */
    public function filterFiles(callable $filter): array;

    /**
     * @param  string  $name
     * @return PathObject|null
     */
    public function getDir(string $name): ?PathObject;

    /**
     * @param  string  $fileName
     * @return FileObject|null
     */
    public function getFile(string $fileName): ?FileObject;

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @param  string  $name
     * @return bool
     */
    public function hasDir(string $name): bool;

    /**
     * @return bool
     */
    public function hasDirs(): bool;

    /**
     * @param  string  $fileName
     * @return bool
     */
    public function hasFile(string $fileName): bool;

    /**
     * @return bool
     */
    public function hasFiles(): bool;

    /**
     * @return SplFileInfo
     */
    public function info(): SplFileInfo;

    /**
     * @return RecursiveDirectoryIterator
     */
    public function iterator(): RecursiveDirectoryIterator;

    /**
     * @param  int  $numberOfJumps
     * @return PathObject
     */
    public function up(int $numberOfJumps = 1): PathObject;
}
