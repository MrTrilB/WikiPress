<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Colors
{
    public static function render(string $content, string $variant = 'primary', string $mode = 'text', array $attributes = [], bool $escape = true): string
    {
        $class = $mode === 'background' || $mode === 'bg'
            ? 'bg-' . $variant
            : 'text-' . $variant;

        return Renderer::component('div', $content, [
            'classes' => [$class],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



