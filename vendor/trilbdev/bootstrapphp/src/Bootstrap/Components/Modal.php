<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Modal
{
    public static function render(string $title, string $body, string $id = 'modalExample', array $options = []): string
    {
        $header = Renderer::component('div', Renderer::component('h5', $title, [
            'classes' => ['modal-title'],
            'attributes' => ['id' => $id . 'Label'],
        ]) . Renderer::component('button', '&times;', [
            'classes' => ['btn-close'],
            'attributes' => [
                'type' => 'button',
                'data-bs-dismiss' => 'modal',
                'aria-label' => 'Close',
            ],
            'escape' => false,
        ]), [
            'classes' => ['modal-header'],
            'escape' => false,
        ]);

        $bodyContent = Renderer::component('div', $body, [
            'classes' => ['modal-body'],
            'escape' => false,
        ]);

        $footer = isset($options['footer']) ? Renderer::component('div', $options['footer'], ['classes' => ['modal-footer'], 'escape' => false]) : '';

        return Renderer::component('div', Renderer::component('div', Renderer::component('div', $header . $bodyContent . $footer, [
            'classes' => ['modal-content'],
            'escape' => false,
        ]), [
            'classes' => ['modal-dialog'],
        ]), [
            'classes' => ['modal', 'fade'],
            'attributes' => array_replace(['id' => $id, 'tabindex' => '-1', 'aria-labelledby' => $id . 'Label', 'aria-hidden' => 'true'], $options['attributes'] ?? []),
            'escape' => false,
        ]);
    }
}



