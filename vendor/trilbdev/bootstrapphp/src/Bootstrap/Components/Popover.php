<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Popover
{
    public static function render(string $title, string $content, string $id = 'popoverExample', array $attributes = []): string
    {
        return Renderer::component('button', $title, [
            'classes' => ['btn', 'btn-secondary'],
            'attributes' => array_replace([
                'type' => 'button',
                'data-bs-toggle' => 'popover',
                'data-bs-content' => $content,
                'id' => $id,
            ], $attributes),
        ]);
    }
}



