<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Sizing
{
    public static function render(string $content, string $property = 'w', string $size = '100', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => [trim($property) . '-' . trim($size)],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



