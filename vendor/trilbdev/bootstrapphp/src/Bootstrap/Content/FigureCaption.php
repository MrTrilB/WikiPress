<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class FigureCaption
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('figcaption', $content, [
            'classes' => ['figure-caption'],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



