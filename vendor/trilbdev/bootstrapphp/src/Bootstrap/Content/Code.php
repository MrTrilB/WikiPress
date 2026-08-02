<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class Code
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('code', $content, [
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



