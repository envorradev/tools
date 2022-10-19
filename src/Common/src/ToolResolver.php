<?php declare(strict_types=1);

namespace Envorra\Tools\Common;

use ReflectionClass;
use ReflectionException;
use Composer\InstalledVersions;
use Envorra\Tools\Composer\PackageFinder;
use Envorra\Tools\Common\Exceptions\ToolNotFoundException;
use Envorra\Tools\Common\Exceptions\ToolResolutionException;
use function array_merge;
use function array_key_exists;

/**
 * ToolResolver
 *
 * @package Envorra\Tools\Common
 */
class ToolResolver
{
    /**
     * @param  array  $map
     */
    public function __construct(protected array $map = [])
    {
        if ($this->hasComposerTool()) {
            $this->map = array_merge($this->map, PackageFinder::getResolutionMap());
        }
    }

    /**
     * @param  string  $abstract
     * @param  string  $concrete
     * @return $this
     */
    public function addResolution(string $abstract, string $concrete): static
    {
        $this->map[$abstract] = $concrete;
        return $this;
    }

    /**
     * @param  array  $resolutions
     * @return $this
     */
    public function addResolutions(array $resolutions): static
    {
        foreach ($resolutions as $abstract => $concrete) {
            $this->addResolution($abstract, $concrete);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function hasComposerTool(): bool
    {
        return InstalledVersions::isInstalled('envorradev/tools') || InstalledVersions::isInstalled('envorradev/tool-composer');
    }

    /**
     * @param  string  $abstract
     * @return bool
     */
    public function resolvable(string $abstract): bool
    {
        return array_key_exists($abstract, $this->map);
    }

    /**
     * @template TTool
     * @param  class-string<TTool>  $abstract
     * @return class-string<TTool>
     * @throws ToolResolutionException
     */
    public function resolve(string $abstract): mixed
    {
        if (isset($this->map[$abstract])) {
            return $this->map[$abstract];
        }

        try {
            if ((new ReflectionClass($abstract))->isInstantiable()) {
                return $abstract;
            }
        } catch (ReflectionException) {
            // skip
        }

        throw new ToolNotFoundException($abstract);
    }
}
