<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class ZIndex
{
    public static function render(string $content, string|int $level = 'auto', ?string $position = null, array $attributes = [], bool $escape = true): string
    {
        $classes = ['z-' . trim((string) $level)];

        if ($position !== null && $position !== '') {
            $classes[] = sprintf('position-%s', trim($position));
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



