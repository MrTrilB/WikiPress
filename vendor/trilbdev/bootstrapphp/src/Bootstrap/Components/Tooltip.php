<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Tooltip
{
    public static function render(string $label, string $title, string $id = 'tooltipExample', array $attributes = []): string
    {
        return Renderer::component('button', $label, [
            'classes' => ['btn', 'btn-secondary'],
            'attributes' => array_replace([
                'type' => 'button',
                'data-bs-toggle' => 'tooltip',
                'title' => $title,
                'id' => $id,
            ], $attributes),
        ]);
    }
}



