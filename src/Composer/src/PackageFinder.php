<?php declare(strict_types=1);

namespace Envorra\Tools\Composer;

use Exception;
use Composer\InstalledVersions;
use Envorra\Tools\Filesystem\Path;
use Envorra\Tools\Filesystem\File;
use Envorra\Tools\Composer\Package\RootPackage;
use Envorra\Tools\Composer\Package\ToolPackage;
use Envorra\Tools\Composer\Package\ComposerPackage;

/**
 * PackageFinder
 *
 * @package Envorra\Tools\Composer
 */
class PackageFinder
{
    public const COMPOSER_FILENAME = 'composer.json';

    protected const TOOL_NAMESPACE = 'envorradev';
    protected const TOOL_PREFIX = 'tool-';
    protected const ALL_TOOLS_PKG = self::TOOL_NAMESPACE.'/tools';
    /** @var array<class-string, class-string> */
    protected array $resolutionMap;
    /** @var array<string, ToolPackage> */
    protected array $tools;

    /**
     * @param  string  $packageName
     * @return ComposerPackage|null
     */
    public static function findPackage(string $packageName): ?ComposerPackage
    {
        return (new self)->find($packageName);
    }

    /**
     * @param  string  $key
     * @return ToolPackage|null
     */
    public static function findTool(string $key): ?ToolPackage
    {
        return (new self)->tool($key);
    }

    /**
     * @return array<class-string, class-string>
     */
    public static function getResolutionMap(): array
    {
        return (new self)->resolutionMap();
    }

    /**
     * @return RootPackage|null
     */
    public static function getRoot(): ?RootPackage
    {
        return (new self)->root();
    }

    /**
     * @param  string  $packageName
     * @return ComposerPackage|null
     */
    public function find(string $packageName): ?ComposerPackage
    {
        if ($file = $this->packageComposerFile($packageName)) {
            try {
                return new ComposerPackage($file);
            } catch (Exception) {
                // continue
            }
        }
        return null;
    }

    /**
     * @param  string  $packageName
     * @return bool
     */
    public function has(string $packageName): bool
    {
        return $this->installed($packageName);
    }

    /**
     * @return bool
     */
    public function hasAllTools(): bool
    {
        return $this->has(self::ALL_TOOLS_PKG);
    }

    /**
     * @param  string  $toolName
     * @return bool
     */
    public function hasTool(string $toolName): bool
    {
        if ($this->hasAllTools()) {
            return true;
        }

        return $this->has($this->toolPackageName($toolName));
    }

    /**
     * @param  string  $packageName
     * @return bool
     */
    public function installed(string $packageName): bool
    {
        return InstalledVersions::isInstalled($packageName);
    }

    /**
     * @return string[]
     */
    public function packages(): array
    {
        return InstalledVersions::getInstalledPackages();
    }

    /**
     * @param  string|null  $packageName
     * @return Path|null
     */
    public function path(?string $packageName = null): ?Path
    {
        $packageName ??= $this->rootPackageName();

        if ($this->installed($packageName)) {
            return new Path(InstalledVersions::getInstallPath($packageName));
        }
        return null;
    }

    /**
     * @param  string|null  $packageName
     * @return string|null
     */
    public function realPath(?string $packageName = null): ?string
    {
        return $this->path($packageName)?->realPath;
    }

    /**
     * @return array<class-string, class-string>
     */
    public function resolutionMap(): array
    {
        if (!isset($this->resolutionMap)) {
            $this->resolutionMap = array_merge(
                ...array_values(
                    array_map(
                        callback: fn($tool) => $tool->resolves,
                        array: (new self)->tools()
                    )
                )
            );
        }
        return $this->resolutionMap;
    }

    /**
     * @return RootPackage|null
     */
    public function root(): ?RootPackage
    {
        $path = $this->path();
        if ($path->hasFile(self::COMPOSER_FILENAME)) {
            try {
                return new RootPackage($path->getFile(self::COMPOSER_FILENAME));
            } catch (Exception) {
                // skip
            }
        }
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rootPackageInfo(): array
    {
        return InstalledVersions::getRootPackage();
    }

    /**
     * @return string|null
     */
    public function rootPackageName(): ?string
    {
        return $this->rootPackageInfo()['name'] ?? null;
    }

    /**
     * @param  string  $name
     * @return ToolPackage|null
     */
    public function tool(string $name): ?ToolPackage
    {
        if ($this->hasAllTools()) {
            $srcPath = $this->path(self::ALL_TOOLS_PKG)?->getDir('src');
            $name = ucwords($this->toolName($name));
            if ($srcPath?->getDir($name)?->hasFile(self::COMPOSER_FILENAME)) {
                try {
                    return new ToolPackage($srcPath->getDir($name)->getFile(self::COMPOSER_FILENAME));
                } catch (Exception) {
                    // continue
                }
            }
        }

        if ($this->hasTool($name)) {
            if ($file = $this->packageComposerFile($this->toolPackageName($name))) {
                try {
                    return new ToolPackage($file);
                } catch (Exception) {
                    // continue
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, ToolPackage>
     */
    public function tools(): array
    {
        if (!isset($this->tools)) {
            if ($this->hasAllTools()) {
                $this->tools = [];
                foreach ($this->path(self::ALL_TOOLS_PKG)->getDir('src')->dirs() as $dir) {
                    if ($dir->hasFile(self::COMPOSER_FILENAME)) {
                        try {
                            $tool = new ToolPackage($dir->getFile(self::COMPOSER_FILENAME));
                            $this->tools[$tool->name] = $tool;
                        } catch (Exception) {
                            continue;
                        }
                    }
                }
                return $this->tools;
            }

            $tools = array_values(
                array_filter(
                    array: $this->packages(),
                    callback: fn($package) => preg_match('~^envorradev/tool-~', $package)
                )
            );

            $this->tools = array_combine(
                keys: $tools,
                values: array_map(
                    callback: fn($tool) => $this->tool($tool),
                    array: $tools
                ),
            );
        }

        return $this->tools;
    }

    /**
     * @param  string|null  $packageName
     * @return string|null
     */
    public function version(?string $packageName = null): ?string
    {
        $packageName ??= $this->rootPackageName();

        if ($this->installed($packageName)) {
            return InstalledVersions::getVersion($packageName);
        }
        return null;
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function namespacedPackage(string $name): string
    {
        $names = explode('/', $name);

        if (count($names) > 1) {
            $name = end($names);
        }

        return self::TOOL_NAMESPACE.'/'.$name;
    }

    /**
     * @param  string  $packageName
     * @return File|null
     */
    protected function packageComposerFile(string $packageName): ?File
    {
        if ($this->installed($packageName)) {
            $path = $this->path($packageName);
            if ($path->hasFile(self::COMPOSER_FILENAME)) {
                return $path->getFile(self::COMPOSER_FILENAME);
            }
        }
        return null;
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function toolName(string $name): string
    {
        return preg_replace(
            pattern: '~('.self::TOOL_NAMESPACE.'/?|'.self::TOOL_PREFIX.')~',
            replacement: '',
            subject: $name
        );
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function toolPackageName(string $name): string
    {
        return $this->namespacedPackage(
            name: self::TOOL_PREFIX.$this->toolName($name),
        );
    }
}
