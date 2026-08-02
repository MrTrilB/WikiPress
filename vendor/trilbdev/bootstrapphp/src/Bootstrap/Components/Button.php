<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Button
{
    public static function render(string $content, string $variant = 'primary', array $attributes = []): string
    {
        return Renderer::component('button', $content, [
            'classes' => ['btn', 'btn-' . $variant],
            'attributes' => array_replace(['type' => 'button'], $attributes),
        ]);
    }
}



