<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class FormText
{
    public static function render(string $content, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $content, [
            'classes' => ['form-text'],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



