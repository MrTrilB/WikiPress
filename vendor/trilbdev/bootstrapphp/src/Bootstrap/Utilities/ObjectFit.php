<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class ObjectFit
{
    public static function render(string $content, string $fit = 'cover', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['object-fit-' . $fit],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



