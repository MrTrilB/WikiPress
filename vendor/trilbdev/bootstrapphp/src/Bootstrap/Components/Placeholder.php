<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Placeholder
{
    public static function render(string $content = '', array $attributes = []): string
    {
        return Renderer::component('span', $content, [
            'classes' => array_merge(['placeholder'], $attributes['classes'] ?? []),
            'attributes' => array_replace($attributes, ['aria-hidden' => 'true']),
        ]);
    }
}



