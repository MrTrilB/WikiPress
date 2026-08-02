<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Overflow
{
    public static function render(string $content, string $axis = 'auto', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['overflow-' . $axis],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



