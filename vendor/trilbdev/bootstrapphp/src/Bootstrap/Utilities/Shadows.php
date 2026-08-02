<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Shadows
{
    public static function render(string $content, string $level = '1', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['shadow-' . $level],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



