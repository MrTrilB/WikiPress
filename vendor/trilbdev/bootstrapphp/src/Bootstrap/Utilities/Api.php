<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Api
{
    public static function classNames(string|array $classes): string
    {
        return Renderer::normalizeClasses($classes);
    }
}



