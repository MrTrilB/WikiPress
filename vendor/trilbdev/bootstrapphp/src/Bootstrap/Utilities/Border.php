<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Border
{
    public static function render(string $content, ?string $variant = null, bool $rounded = false, array $attributes = [], bool $escape = true): string
    {
        $classes = ['border'];

        if ($variant !== null && $variant !== '') {
            $classes[] = 'border-' . $variant;
        }

        if ($rounded) {
            $classes[] = 'rounded';
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



