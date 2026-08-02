<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class Stack
{
    public static function render(string $content, string|array $gap = '3', array $attributes = []): string
    {
        $classes = ['stack'];

        if (is_array($gap)) {
            foreach ($gap as $breakpoint => $size) {
                $breakpoint = trim((string) $breakpoint);
                $size = trim((string) $size);

                if ($breakpoint === '' || $breakpoint === 'default') {
                    $classes[] = sprintf('gap-%s', $size);
                } else {
                    $classes[] = sprintf('gap-%s-%s', $breakpoint, $size);
                }
            }
        } else {
            $classes[] = sprintf('gap-%s', trim($gap));
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



