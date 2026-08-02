<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class StretchedLink
{
    public static function render(string $content, string $href = '#', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('a', $content, [
            'classes' => ['stretched-link'],
            'attributes' => array_replace(['href' => $href], $attributes),
            'escape' => $escape,
        ]);
    }
}



