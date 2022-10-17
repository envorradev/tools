<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Contracts\Json;

use JsonSerializable;

/**
 * ConvertsToJson
 *
 * @package Envorra\Tools\Common\Contracts\Json
 */
interface ConvertsToJson extends JsonSerializable
{
    /**
     * @return string
     */
    public function toJson(): string;

    /**
     * @return string
     */
    public function toPrettyJson(): string;

    /**
     * @return JsonDataObject
     */
    public function toJsonObject(): JsonDataObject;
}
