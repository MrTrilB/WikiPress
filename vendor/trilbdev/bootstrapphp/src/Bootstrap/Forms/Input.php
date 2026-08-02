<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class Input
{
    public static function render(string $name, string $value = '', array $attributes = []): string
    {
        return Renderer::component('input', '', [
            'attributes' => array_replace(
                [
                    'type' => 'text',
                    'name' => $name,
                    'value' => $value,
                ],
                $attributes
            ),
            'void' => true,
        ]);
    }
}



