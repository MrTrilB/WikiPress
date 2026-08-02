<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Toast
{
    public static function render(string $title, string $body, array $options = []): string
    {
        $header = Renderer::component('div', Renderer::component('strong', $title, ['classes' => ['me-auto']]) . Renderer::component('button', '&times;', [
            'classes' => ['btn-close'],
            'attributes' => [
                'type' => 'button',
                'data-bs-dismiss' => 'toast',
                'aria-label' => 'Close',
            ],
            'escape' => false,
        ]), [
            'classes' => ['toast-header'],
            'escape' => false,
        ]);

        $bodyContent = Renderer::component('div', $body, [
            'classes' => ['toast-body'],
            'escape' => false,
        ]);

        return Renderer::component('div', $header . $bodyContent, [
            'classes' => ['toast'],
            'attributes' => $options['attributes'] ?? [],
            'escape' => false,
        ]);
    }
}



