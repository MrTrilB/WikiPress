<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class CodeBlock
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('pre', Renderer::component('code', $content, [
            'attributes' => $attributes,
            'escape' => $escape,
        ]), [
            'escape' => false,
        ]);
    }
}



