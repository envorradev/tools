<?php declare(strict_types=1);

namespace Envorra\Tools\Composer\Package;

use BadMethodCallException;
use Envorra\Tools\Json\Json;
use Composer\InstalledVersions;
use Envorra\Tools\Filesystem\File;
use Envorra\Tools\Filesystem\Path;
use Envorra\Tools\Composer\PackageFinder;
use Envorra\Tools\Composer\ComposerSchema;
use Envorra\Tools\Composer\Exceptions\UnexpectedFileException;
use function in_array;
use function array_map;
use function array_keys;
use function strtolower;
use function array_merge;
use function str_replace;
use function array_values;
use function str_contains;
use function preg_replace;
use function array_combine;
use function str_ends_with;
use function method_exists;
use function property_exists;

/**
 * AbstractComposerPackage
 *
 * @package Envorra\Tools\Composer
 *
 * @property-read Path $installPath
 * @property-read Path $path
 */
abstract class AbstractComposerPackage
{
    public readonly array $aliases;
    public readonly array $extra;
    public readonly bool $installed;
    public readonly Json $json;
    public readonly string $name;
    public readonly string $prettyVersion;
    public readonly string|null $reference;
    public readonly bool $root;
    public readonly string $type;
    public readonly string $version;
    protected PackageFinder $packageFinder;
    protected readonly array $rawPackageData;
    protected readonly ComposerSchema $schema;

    /**
     * @param  File  $file
     * @throws UnexpectedFileException
     */
    public function __construct(public readonly File $file)
    {
        if (strtolower($this->file->fileName) !== PackageFinder::COMPOSER_FILENAME) {
            throw new UnexpectedFileException('Invalid composer file');
        }

        $this->packageFinder = new PackageFinder();

        $this->schema = ComposerSchema::instance();
        $this->json = new Json($this->file->contents());
        $this->name = $this->json->get('name');
        $this->extra = $this->json->get('extra') ?? [];

        $this->installed = $this->packageFinder->installed($this->name);
        $this->root = $this->name === $this->packageFinder->rootPackageName();

        if ($this->installed) {
            if ($this->root) {
                $this->rawPackageData = InstalledVersions::getAllRawData()[0]['root'];
            } else {
                $this->rawPackageData = InstalledVersions::getAllRawData()[0]['versions'][$this->name];
            }

            $this->version = $this->rawPackageData['version'];
            $this->prettyVersion = $this->rawPackageData['pretty_version'];
            $this->type = $this->rawPackageData['type'];
            $this->aliases = $this->rawPackageData['aliases'];
            $this->reference = $this->rawPackageData['reference'];
        }
    }

    /**
     * @param  string  $method
     * @param  array   $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists($this->file, $method)) {
            return $this->file->$method(...$arguments);
        }

        if (method_exists($this->json, $method)) {
            return $this->json->$method(...$arguments);
        }

        throw new BadMethodCallException();
    }

    /**
     * @param  string  $property
     * @return mixed
     */
    public function __get(string $property): mixed
    {
        if ($property === 'installPath' || $property === 'path') {
            return $this->path();
        }

        if (in_array($property, $this->schema->properties())) {
            return $this->json->$property;
        }

        if (property_exists($this->file, $property)) {
            return $this->file->$property;
        }

        $property = strtolower(preg_replace('/([A-Z])/', '_$1', $property));

        if (str_contains($property, '_')) {
            return $this->__get(str_replace('_', '-', $property));
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    public function autoloaded(): array
    {
        return array_merge(
            $this->get('autoload.psr-4') ?? [],
            $this->get('autoload.psr-0') ?? [],
            $this->get('autoload-dev.psr-4') ?? [],
            $this->get('autoload-dev.psr-0') ?? [],
        );
    }

    /**
     * @param  string|int  $key
     * @return mixed
     */
    public function get(string|int $key): mixed
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return $this->json->get($key);
    }

    /**
     * @param  string  $namespace
     * @return bool
     */
    public function hasNamespace(string $namespace): bool
    {
        $namespace = str_ends_with($namespace, '\\') ? $namespace : $namespace.'\\';
        return in_array($namespace, $this->namespaces());
    }

    /**
     * @return Path
     */
    public function installPath(): Path
    {
        return $this->path();
    }

    /**
     * @param  string  $namespace
     * @return Path|null
     */
    public function namespacePath(string $namespace): ?Path
    {
        $namespace = str_ends_with($namespace, '\\') ? $namespace : $namespace.'\\';
        if ($this->hasNamespace($namespace)) {
            return $this->namespacePathMap()[$namespace];
        }
        return null;
    }

    /**
     * @return array<string, Path>
     */
    public function namespacePathMap(): array
    {
        return array_combine($this->namespaces(), $this->paths());
    }

    /**
     * @param  string  $namespace
     * @return string|null
     */
    public function namespaceRealPath(string $namespace): ?string
    {
        return $this->namespacePath($namespace)?->realPath;
    }

    /**
     * @return array<string, string>
     */
    public function namespaceRealPathMap(): array
    {
        return array_combine($this->namespaces(), $this->realPaths());
    }

    /**
     * @return string[]
     */
    public function namespaces(): array
    {
        return array_keys($this->autoloaded());
    }

    /**
     * @return Path
     */
    public function path(): Path
    {
        return $this->file->path;
    }

    /**
     * @return Path[]
     */
    public function paths(): array
    {
        return array_map(
            callback: fn(string $path) => new Path($path, $this->file->path),
            array: array_values($this->autoloaded())
        );
    }

    /**
     * @return string[]
     */
    public function realPaths(): array
    {
        return array_map(fn(Path $path) => $path->realPath, $this->paths());
    }

    /**
     * @return array
     */
    public function requirements(): array
    {
        return array_merge($this->get('require') ?? [], $this->get('require-dev') ?? []);
    }
}
