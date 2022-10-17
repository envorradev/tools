<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Json;

use stdClass;
use Envorra\Tools\Common\Contracts\DataObject;
use Envorra\Tools\Common\Contracts\Arrays\ConvertsToArray;
use Envorra\Tools\Common\Contracts\Strings\ConvertsToString;

/**
 * JsonDataObject
 *
 * @package Envorra\Tools\Common\Contracts\Json
 *
 * @extends DataObject<string>
 */
interface JsonDataObject extends DataObject, ConvertsToArray, ConvertsToString
{
    /**
     * @param  mixed  $json
     * @return bool
     */
    public static function isValid(mixed $json): bool;

    /**
     * @param  mixed  $data
     * @return static
     */
    public static function make(mixed $data): static;

    /**
     * @param  int  $flag
     * @return static
     */
    public function addEncodeFlag(int $flag): static;

    /**
     * @return string
     */
    public function getJson(): string;

    /**
     * @return static
     */
    public function minify(): static;

    /**
     * @return static
     */
    public function pretty(): static;

    /**
     * @return static
     */
    public function removeEncodeFlags(): static;

    /**
     * @return stdClass
     */
    public function toObject(): stdClass;
}
