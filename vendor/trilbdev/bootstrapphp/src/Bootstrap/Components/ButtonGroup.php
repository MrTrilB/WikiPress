<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class ButtonGroup
{
    public static function render(array $buttons, array $attributes = [], bool $vertical = false): string
    {
        $buttonHtml = '';

        foreach ($buttons as $button) {
            if (is_string($button)) {
                $buttonHtml .= $button;
                continue;
            }

            $buttonHtml .= Renderer::component('button', (string) ($button['label'] ?? ''), [
                'classes' => array_merge(['btn'], $button['classes'] ?? []),
                'attributes' => array_replace(['type' => 'button'], $button['attributes'] ?? []),
            ]);
        }

        return Renderer::component('div', $buttonHtml, [
            'classes' => ['btn-group', $vertical ? 'btn-group-vertical' : ''],
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



