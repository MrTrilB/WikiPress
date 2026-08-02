<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class StackHelper
{
    public static function render(string $content, string $direction = 'vertical', string $gap = '3', array $attributes = [], bool $escape = false): string
    {
        $classes = ['stack', $direction === 'horizontal' ? 'flex-row' : 'flex-column', 'gap-' . $gap];

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



