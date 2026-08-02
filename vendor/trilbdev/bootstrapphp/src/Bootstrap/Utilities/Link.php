<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Link
{
    public static function render(string $content, string $variant = 'primary', array $attributes = [], bool $escaped = true): string
    {
        return Renderer::component('a', $content, [
            'classes' => ['link-' . $variant],
            'attributes' => $attributes,
            'escape' => $escaped,
        ]);
    }
}



