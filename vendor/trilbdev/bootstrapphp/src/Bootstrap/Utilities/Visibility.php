<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;

final class Visibility
{
    public static function render(string $content, string $state = 'visible', array $attributes = [], bool $escape = true): string
    {
        $class = $state === 'invisible' ? 'invisible' : 'visible';

        return Renderer::component('div', $content, [
            'classes' => [$class],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



