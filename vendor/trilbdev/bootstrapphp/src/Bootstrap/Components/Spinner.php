<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Spinner
{
    public static function render(string $variant = 'primary', array $attributes = []): string
    {
        return Renderer::component('div', '', [
            'classes' => ['spinner-border', 'text-' . $variant],
            'attributes' => array_replace(['role' => 'status'], $attributes),
        ]);
    }
}



