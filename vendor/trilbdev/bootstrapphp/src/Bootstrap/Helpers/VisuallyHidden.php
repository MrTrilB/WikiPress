<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class VisuallyHidden
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('span', $content, [
            'classes' => ['visually-hidden'],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



