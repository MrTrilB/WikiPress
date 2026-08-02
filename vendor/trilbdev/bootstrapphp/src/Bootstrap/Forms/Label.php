<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class Label
{
    public static function render(string $content, array $attributes = []): string
    {
        return Renderer::component('label', $content, [
            'attributes' => array_replace([
                'class' => 'form-label',
            ], $attributes),
            'escape' => true,
        ]);
    }
}



