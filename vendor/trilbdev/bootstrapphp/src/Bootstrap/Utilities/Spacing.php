<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Spacing
{
    public static function render(
        string $content,
        string $type = 'm',
        string $position = '',
        string $size = '3',
        array $attributes = [],
        bool $escape = true
    ): string {
        $position = trim($position);
        $classes = [sprintf('%s%s-%s', $type, $position, $size)];

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



