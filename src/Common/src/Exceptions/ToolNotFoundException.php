<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Exceptions;

/**
 * ToolNotFoundException
 *
 * @package Envorra\Tools\Common\Exceptions
 */
class ToolNotFoundException extends ToolResolutionException
{
    public function __construct(string $abstract)
    {
        parent::__construct($abstract.' was not found.');
    }
}
