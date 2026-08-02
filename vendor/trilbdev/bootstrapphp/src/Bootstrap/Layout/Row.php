<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class Row
{
    public static function render(
        string $content,
        array $attributes = [],
        ?string $gutter = null,
        ?string $gutterX = null,
        ?string $gutterY = null,
        ?string $align = null,
        ?string $justify = null
    ): string {
        $classes = ['row'];

        if ($gutter !== null && $gutter !== '') {
            $classes[] = sprintf('g-%s', $gutter);
        }

        if ($gutterX !== null && $gutterX !== '') {
            $classes[] = sprintf('gx-%s', $gutterX);
        }

        if ($gutterY !== null && $gutterY !== '') {
            $classes[] = sprintf('gy-%s', $gutterY);
        }

        if ($align !== null && $align !== '') {
            $classes[] = sprintf('align-items-%s', $align);
        }

        if ($justify !== null && $justify !== '') {
            $classes[] = sprintf('justify-content-%s', $justify);
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



