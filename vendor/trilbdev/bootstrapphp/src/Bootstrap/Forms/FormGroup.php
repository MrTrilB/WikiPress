<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class FormGroup
{
    public static function render(string $content, array $attributes = []): string
    {
        return Renderer::component('div', $content, [
            'attributes' => array_replace([
                'class' => 'mb-3',
            ], $attributes),
            'escape' => false,
        ]);
    }
}



