<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Badge
{
    public static function render(string $content, string $variant = 'primary', array $attributes = []): string
    {
        return Renderer::component('span', $content, [
            'classes' => ['badge', 'bg-' . $variant],
            'attributes' => $attributes,
        ]);
    }
}



