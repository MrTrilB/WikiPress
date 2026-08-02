<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Collapse
{
    public static function render(string $content, string $id, bool $show = false, array $attributes = []): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['collapse', $show ? 'show' : ''],
            'attributes' => array_replace(['id' => $id], $attributes),
            'escape' => false,
        ]);
    }
}



