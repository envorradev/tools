<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Filesystem;

use SplFileInfo;
use Envorra\Tools\Common\Contracts\Strings\ConvertsToString;

/**
 * FileObject
 *
 * @package Envorra\Tools\Common\Contracts\Filesystem
 *
 * @extends ConvertsToString
 *
 * @mixin SplFileInfo
 */
interface FileObject extends ConvertsToString
{
    /**
     * @param  SplFileInfo|string|null  $fileInfo
     */
    public function __construct(SplFileInfo|string|null $fileInfo = null);

    /**
     * @param  string  $method
     * @param  array   $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments): mixed;

    /**
     * @return string|null
     */
    public function contents(): string|null;

    /**
     * @return SplFileInfo
     */
    public function info(): SplFileInfo;
}
