<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class Textarea
{
    public static function render(string $name, string $content = '', array $attributes = []): string
    {
        return Renderer::component('textarea', $content, [
            'attributes' => array_replace(['name' => $name], $attributes),
            'escape' => true,
        ]);
    }
}



