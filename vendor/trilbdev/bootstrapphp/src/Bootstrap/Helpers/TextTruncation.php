<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class TextTruncation
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('p', $content, [
            'classes' => ['text-truncate'],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



