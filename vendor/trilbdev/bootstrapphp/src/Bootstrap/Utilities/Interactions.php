<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Interactions
{
    public static function render(string $content, string $kind = 'pointer', string $value = 'auto', array $attributes = [], bool $escape = true): string
    {
        $classes = [];

        if ($kind === 'pointer') {
            $classes[] = 'pe-' . trim($value);
        } elseif ($kind === 'user-select') {
            $classes[] = 'user-select-' . trim($value);
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



