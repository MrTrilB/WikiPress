<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class Grid
{
    public static function render(string $content, string|int|array $columns = 12, ?string $gutter = null, array $attributes = []): string
    {
        $classes = [];

        if (is_array($columns)) {
            foreach ($columns as $breakpoint => $count) {
                $breakpoint = trim((string) $breakpoint);
                $count = trim((string) $count);

                if ($breakpoint === '' || $breakpoint === 'default') {
                    $classes[] = sprintf('row-cols-%s', $count);
                } else {
                    $classes[] = sprintf('row-cols-%s-%s', $breakpoint, $count);
                }
            }
        } else {
            $classes[] = sprintf('row-cols-%s', trim((string) $columns));
        }

        if ($gutter !== null && $gutter !== '') {
            $classes[] = sprintf('g-%s', $gutter);
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



