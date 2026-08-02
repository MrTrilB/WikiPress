<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class VerticalAlign
{
    public static function render(string $content, string $alignment = 'middle', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['align-self-' . $alignment],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



