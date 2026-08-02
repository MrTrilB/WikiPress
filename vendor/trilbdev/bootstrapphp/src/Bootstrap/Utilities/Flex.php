<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Flex
{
    public static function render(string $content, string|array $direction = 'row', bool $wrap = false, ?string $justify = null, ?string $align = null, ?string $gap = null, array $attributes = []): string
    {
        $classes = ['d-flex'];

        if (is_array($direction)) {
            foreach ($direction as $breakpoint => $value) {
                $classes[] = sprintf('flex-%s-%s', $breakpoint, trim((string) $value));
            }
        } else {
            $classes[] = 'flex-' . trim($direction);
        }

        if ($wrap) {
            $classes[] = 'flex-wrap';
        }

        if ($justify !== null && $justify !== '') {
            $classes[] = 'justify-content-' . trim($justify);
        }

        if ($align !== null && $align !== '') {
            $classes[] = 'align-items-' . trim($align);
        }

        if ($gap !== null && $gap !== '') {
            $classes[] = 'gap-' . trim($gap);
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



