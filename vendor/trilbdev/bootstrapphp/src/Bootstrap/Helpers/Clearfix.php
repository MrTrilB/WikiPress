<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

use BootstrapPHP\Includes\Renderer;

final class Clearfix
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['clearfix'],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



