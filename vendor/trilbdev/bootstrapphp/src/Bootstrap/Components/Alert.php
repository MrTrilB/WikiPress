<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Alert
{
    public static function render(string $content, string $variant = 'primary', array $attributes = []): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['alert', 'alert-' . $variant],
            'attributes' => array_replace(['role' => 'alert'], $attributes),
        ]);
    }
}



