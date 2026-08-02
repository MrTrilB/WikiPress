<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Card
{
    public static function render(string $body, array $options = []): string
    {
        $header = isset($options['header']) ? Renderer::component('div', $options['header'], ['classes' => ['card-header']]) : '';
        $footer = isset($options['footer']) ? Renderer::component('div', $options['footer'], ['classes' => ['card-footer']]) : '';
        $bodyContent = Renderer::component('div', $body, ['classes' => ['card-body']]);

        return Renderer::component('div', $header . $bodyContent . $footer, [
            'classes' => ['card', ...($options['classes'] ?? [])],
            'attributes' => $options['attributes'] ?? [],
            'escape' => false,
        ]);
    }
}



