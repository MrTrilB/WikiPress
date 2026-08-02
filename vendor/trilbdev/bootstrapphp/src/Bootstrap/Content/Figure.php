<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class Figure
{
    public static function render(string $content, array $attributes = []): string
    {
        return Renderer::component('figure', $content, [
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



