<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Text
{
    public static function render(
        string $content,
        ?string $alignment = null,
        ?string $color = null,
        array $attributes = [],
        bool $escape = true
    ): string {
        $classes = [];

        if ($alignment !== null && $alignment !== '') {
            $classes[] = 'text-' . $alignment;
        }

        if ($color !== null && $color !== '') {
            $classes[] = 'text-' . $color;
        }

        return Renderer::component('p', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



