<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class Ratio
{
    public static function render(string $content, string $ratio = '16x9', array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['ratio', 'ratio-' . $ratio],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



