<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class VerticalRule
{
    public static function render(array $attributes = []): string
    {
        return Renderer::component('div', '', [
            'classes' => ['vr'],
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



