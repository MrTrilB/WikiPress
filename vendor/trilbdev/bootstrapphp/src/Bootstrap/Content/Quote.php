<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class Quote
{
    public static function render(string $content, array $attributes = [], bool $reverse = false, bool $escape = true): string
    {
        $classes = [];
        if ($reverse) {
            $classes[] = 'blockquote-reverse';
        }

        return Renderer::component('blockquote', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



