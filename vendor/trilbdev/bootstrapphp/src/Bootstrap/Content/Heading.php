<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class Heading
{
    public static function render(string $content, int $level = 1, array $attributes = []): string
    {
        $level = min(max($level, 1), 6);

        return Renderer::component(sprintf('h%d', $level), $content, [
            'attributes' => $attributes,
        ]);
    }
}



