<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Opacity
{
    public static function render(string $content, string $level = '100', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['opacity-' . $level],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



