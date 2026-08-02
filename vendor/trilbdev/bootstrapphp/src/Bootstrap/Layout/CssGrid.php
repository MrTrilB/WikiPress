<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class CssGrid
{
    public static function render(string $content, string|array|null $columns = null, ?string $gap = null, array $attributes = [], bool $escape = false): string
    {
        $classes = ['d-grid'];
        $style = '';

        if ($gap !== null && $gap !== '') {
            $classes[] = sprintf('gap-%s', $gap);
        }

        if ($columns !== null) {
            if (is_array($columns)) {
                $style = 'grid-template-columns: ' . implode(' ', $columns) . ';';
            } else {
                $style = 'grid-template-columns: ' . trim($columns) . ';';
            }
        }

        if ($style !== '') {
            $attributes['style'] = trim(($attributes['style'] ?? '') . ' ' . $style);
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



