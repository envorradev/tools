<?php declare(strict_types=1);

namespace Envorra\Tools\Composer\Package;

use Exception;
use Envorra\Tools\Filesystem\File;
use Envorra\Tools\Composer\Exceptions\UnexpectedFileException;
use Envorra\Tools\Composer\Exceptions\PackageNotInstalledException;

/**
 * RootPackage
 *
 * @package Envorra\Tools\Composer
 */
class RootPackage extends AbstractComposerPackage
{
    public readonly bool $dev;

    /**
     * @param  File  $file
     * @throws UnexpectedFileException|PackageNotInstalledException|Exception
     */
    public function __construct(File $file)
    {
        parent::__construct($file);
        $this->dev = $this->rawPackageData['dev'] ?? false;

        if(!$this->isRootPackage) {
            throw new Exception($this->name.' is not the root package!');
        }
    }

    /**
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packageFinder->packages();
    }

    /**
     * @param  string  $packageName
     * @return bool
     */
    public function hasPackage(string $packageName): bool
    {
        return $this->packageFinder->installed($packageName);
    }

    /**
     * @param  string  $packageName
     * @return ComposerPackage|null
     */
    public function getPackage(string $packageName): ?ComposerPackage
    {
        if($this->packageFinder->installed($packageName)) {
            return $this->packageFinder->find($packageName);
        }
        return null;
    }
}
