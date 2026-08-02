<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class Container
{
    private const BREAKPOINTS = ['sm', 'md', 'lg', 'xl', 'xxl'];

    public static function render(string $content, string|bool $size = false, array $attributes = [], bool $escape = true): string
    {
        $classes = ['container'];

        if ($size === true || $size === 'fluid') {
            $classes = ['container-fluid'];
        } elseif (is_string($size) && $size !== false && $size !== '') {
            $normalized = strtolower(trim($size));
            if (in_array($normalized, self::BREAKPOINTS, true)) {
                $classes = ['container-' . $normalized];
            } elseif ($normalized === 'sm' || $normalized === 'md' || $normalized === 'lg' || $normalized === 'xl' || $normalized === 'xxl') {
                $classes = ['container-' . $normalized];
            } else {
                $classes = ['container'];
            }
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



