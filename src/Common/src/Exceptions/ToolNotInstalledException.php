<?php declare(strict_types=1);

namespace Envorra\Tools\Common\Exceptions;

/**
 * ToolNotInstalledException
 *
 * @package Envorra\Tools\Common\Exceptions
 */
class ToolNotInstalledException extends ToolResolutionException
{

    public function __construct(string $abstract)
    {
        parent::__construct($abstract.' is not installed.');
    }
}
