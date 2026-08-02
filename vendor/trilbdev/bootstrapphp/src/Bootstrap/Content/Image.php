<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class Image
{
    public static function render(string $src, string $alt = '', array $attributes = []): string
    {
        return Renderer::component('img', '', [
            'attributes' => array_replace(
                [
                    'src' => $src,
                    'alt' => $alt,
                ],
                $attributes
            ),
            'void' => true,
        ]);
    }
}



