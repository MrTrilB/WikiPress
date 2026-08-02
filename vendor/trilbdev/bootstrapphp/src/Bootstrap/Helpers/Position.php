<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class Position
{
    public static function render(string $content, string $position = 'relative', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['position-' . $position],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



