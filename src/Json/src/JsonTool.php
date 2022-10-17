<?php declare(strict_types=1);

namespace Envorra\Tools\Json;

use stdClass;
use Envorra\Tools\Common\Contracts\Json\JsonSerializer;

/**
 * JsonTool
 *
 * @package Envorra\Tools\Json
 */
class JsonTool implements JsonSerializer
{
    /**
     * @inheritDoc
     */
    public static function encode(mixed $data, int $flags = 0): string
    {
        if(self::isJson($data)) {
            $data = self::decode($data);
        }
        return json_encode($data, $flags);
    }

    /**
     * @inheritDoc
     */
    public static function decode(string $json, bool|null $associative = null): array|stdClass
    {
        return json_decode($json, $associative);
    }

    /**
     * @inheritDoc
     */
    public static function toObject(string $json): stdClass
    {
        return (object) self::decode($json, false);
    }

    /**
     * @inheritDoc
     */
    public static function toArray(string $json): array
    {
        return (array) self::decode($json, true);
    }

    /**
     * @inheritDoc
     */
    public static function isJson(mixed $data): bool
    {
        if(is_string($data)) {
            json_decode($data);
            return json_last_error() === JSON_ERROR_NONE;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public static function pretty(mixed $data): string
    {
        return self::encode($data, JSON_PRETTY_PRINT);
    }

}
