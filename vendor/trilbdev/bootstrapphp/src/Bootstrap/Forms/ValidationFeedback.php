<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class ValidationFeedback
{
    public static function render(string $message, bool $valid = false, array $attributes = [], bool $escape = true): string
    {
        return Renderer::component('div', $message, [
            'classes' => [$valid ? 'valid-feedback' : 'invalid-feedback'],
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }
}



