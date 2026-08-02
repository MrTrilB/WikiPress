<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Offcanvas
{
    public static function render(string $title, string $body, string $id = 'offcanvasExample', array $options = []): string
    {
        $header = Renderer::component('div', Renderer::component('h5', $title, [
            'classes' => ['offcanvas-title'],
            'attributes' => ['id' => $id . 'Label'],
        ]) . Renderer::component('button', '&times;', [
            'classes' => ['btn-close', 'text-reset'],
            'attributes' => [
                'type' => 'button',
                'data-bs-dismiss' => 'offcanvas',
                'aria-label' => 'Close',
            ],
            'escape' => false,
        ]), [
            'classes' => ['offcanvas-header'],
            'escape' => false,
        ]);

        $bodyContent = Renderer::component('div', $body, [
            'classes' => ['offcanvas-body'],
            'escape' => false,
        ]);

        return Renderer::component('div', Renderer::component('div', $header . $bodyContent, [
            'classes' => ['offcanvas-content'],
            'escape' => false,
        ]), [
            'classes' => ['offcanvas', 'offcanvas-start'],
            'attributes' => array_replace(['tabindex' => '-1', 'id' => $id, 'aria-labelledby' => $id . 'Label'], $options['attributes'] ?? []),
            'escape' => false,
        ]);
    }
}



