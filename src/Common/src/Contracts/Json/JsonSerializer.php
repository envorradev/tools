<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Json;

use stdClass;

/**
 * JsonSerializer
 *
 * @package Envorra\Tools\Common\Contracts\Json
 */
interface JsonSerializer
{
    /**
     * @param  string  $json
     * @return array|stdClass
     */
    public static function decode(string $json): array|stdClass;

    /**
     * @param  mixed  $data
     * @param  int    $flags
     * @return string
     */
    public static function encode(mixed $data, int $flags = 0): string;

    /**
     * @param  mixed  $data
     * @return bool
     */
    public static function isJson(mixed $data): bool;

    /**
     * @param  mixed  $data
     * @return string
     */
    public static function pretty(mixed $data): string;

    /**
     * @param  string  $json
     * @return array
     */
    public static function toArray(string $json): array;

    /**
     * @param  string  $json
     * @return stdClass
     */
    public static function toObject(string $json): stdClass;
}
