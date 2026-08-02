<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class FileInput
{
    public static function render(string $name, array $attributes = []): string
    {
        return Renderer::component('input', '', [
            'attributes' => array_replace([
                'type' => 'file',
                'name' => $name,
                'class' => 'form-control',
            ], $attributes),
            'void' => true,
        ]);
    }
}



