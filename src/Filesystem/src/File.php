<?php declare(strict_types=1);

namespace Envorra\Tools\Filesystem;

use SplFileInfo;
use Envorra\Tools\Common\Contracts\Filesystem\FileObject;

/**
 * File
 *
 * @package Envorra\Tools\Filesystem
 *
 * @implements FileObject
 *
 * @mixin SplFileInfo
 */
class File implements FileObject
{
    public readonly string $enclosingFolder;
    public readonly string $extension;
    public readonly string $fileName;
    public readonly string $name;
    public readonly Path $path;
    public readonly string $realPath;
    protected readonly SplFileInfo $info;

    /**
     * @inheritDoc
     */
    public function __construct(SplFileInfo|string|null $fileInfo = null)
    {
        if (!$fileInfo) {
            $fileInfo = __FILE__;
        }

        if (is_string($fileInfo)) {
            $fileInfo = new SplFileInfo($fileInfo);
        }

        $this->info = $fileInfo;
        $this->extension = $this->info->getExtension();
        $this->name = $this->info->getBasename('.'.$this->extension);
        $this->fileName = $this->info->getFilename();
        $this->realPath = $this->info->getRealPath() ?: '';
        $this->path = new Path(str_replace(DIRECTORY_SEPARATOR.$this->fileName, '', $this->realPath));
        $this->enclosingFolder = $this->path->name;
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists($this->info, $method)) {
            return $this->info->$method(...$arguments);
        }
        return null;
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
    public function contents(): string|null
    {
        if ($this->isReadable()) {
            return $this->openFile()->fread($this->getSize());
        }
        return null;
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
    public function toString(): string
    {
        return $this->realPath;
    }


}
