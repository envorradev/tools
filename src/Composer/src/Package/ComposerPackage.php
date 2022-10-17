<?php declare(strict_types=1);

namespace Envorra\Tools\Composer\Package;


use Envorra\Tools\Filesystem\File;

/**
 * ComposerPackage
 *
 * @package Envorra\Tools\Composer
 */
class ComposerPackage extends AbstractComposerPackage
{
    public readonly bool $devRequirement;

    public function __construct(File $file)
    {
        parent::__construct($file);
        $this->devRequirement = $this->rawPackageData['dev_requirement'] ?? false;
    }

    /**
     * @return RootPackage
     */
    public function getRootPackage(): RootPackage
    {
        return $this->packageFinder->root();
    }
}
