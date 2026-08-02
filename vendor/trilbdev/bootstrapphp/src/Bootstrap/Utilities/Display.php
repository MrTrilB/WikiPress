<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Display
{
    public static function render(string $content, string $display = 'block', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['d-' . $display],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



