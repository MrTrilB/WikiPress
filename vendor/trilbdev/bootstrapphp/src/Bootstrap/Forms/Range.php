<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class Range
{
    public static function render(string $name, int $value = 0, int $min = 0, int $max = 100, int $step = 1, array $attributes = []): string
    {
        return Renderer::component('input', '', [
            'attributes' => array_replace([
                'type' => 'range',
                'name' => $name,
                'value' => (string) $value,
                'min' => (string) $min,
                'max' => (string) $max,
                'step' => (string) $step,
                'class' => 'form-range',
            ], $attributes),
            'void' => true,
        ]);
    }
}



