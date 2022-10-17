<?php declare(strict_types=1);

namespace Envorra\Tools\Composer;

use Envorra\Tools\Json\Json;

/**
 * Schema
 *
 * @package Envorra\Tools\Composer
 */
class ComposerSchema
{
    /**
     * URL to the schema.json.
     */
    public const URL = 'https://getcomposer.org/schema.json';
    /**
     * Path to the cached schema.json.
     */
    public const CACHE_FILE = __DIR__.'/../cached/schema.json';

    /**
     * Max cached schema.json age to be used with filemtime().
     *
     * YEAR: 31536000
     * MONTH: 2592000
     * WEEK: 604800
     * DAY: 86400
     * HOUR: 3600
     * MINUTE: 60
     * SECOND: 1
     */
    public const MAX_AGE = 7 * 86400;

    /**
     * @var self
     */
    protected static self $instance;

    /**
     * @var Json
     */
    public readonly Json $json;

    /**
     * Schema constructor.
     */
    private function __construct()
    {
        if (!file_exists(self::CACHE_FILE) || time() - filemtime(self::CACHE_FILE) > self::MAX_AGE) {
            file_put_contents(self::CACHE_FILE, file_get_contents(self::URL));
        }

        $this->json = new Json(file_get_contents(self::CACHE_FILE));
    }

    /**
     * @return ComposerSchema
     */
    public static function instance(): ComposerSchema
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param  string|int  $key
     * @return mixed
     */
    public function get(string|int $key): mixed
    {
        return $this->json->get($key);
    }

    /**
     * @return array
     */
    public function properties(): array
    {
        return array_keys($this->get('properties'));
    }

    /**
     * @param  string  $name
     * @return array
     */
    public function property(string $name): array
    {
        return $this->get('properties.'.$name);
    }
}
