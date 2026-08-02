<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Progress
{
    public static function render(int $value, int $max = 100, string $variant = 'primary', array $attributes = []): string
    {
        $bar = Renderer::component('div', (string) $value . '%', [
            'classes' => ['progress-bar', 'bg-' . $variant],
            'attributes' => [
                'role' => 'progressbar',
                'style' => 'width: ' . $value . '%;',
                'aria-valuenow' => (string) $value,
                'aria-valuemin' => '0',
                'aria-valuemax' => (string) $max,
            ],
        ]);

        return Renderer::component('div', $bar, [
            'classes' => ['progress'],
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



