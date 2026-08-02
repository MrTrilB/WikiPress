<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Background
{
    public static function render(string $content, string $variant = 'primary', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['bg-' . $variant],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



