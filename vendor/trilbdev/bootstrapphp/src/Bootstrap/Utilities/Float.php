<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class FloatHelper
{
    public static function render(string $content, string|array $direction = 'start', array $attributes = [], bool $escape = true): string
    {
        $classes = [];

        if (is_array($direction)) {
            foreach ($direction as $breakpoint => $value) {
                $breakpoint = trim((string) $breakpoint);
                $value = trim((string) $value);
                $classes[] = $breakpoint === '' ? sprintf('float-%s', $value) : sprintf('float-%s-%s', $breakpoint, $value);
            }
        } else {
            $classes[] = sprintf('float-%s', trim($direction));
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



