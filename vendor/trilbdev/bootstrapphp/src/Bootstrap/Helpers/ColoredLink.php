<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class ColoredLink
{
    public static function render(string $content, string $color = 'primary', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('a', $content, [
            'classes' => ['link-' . $color],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



