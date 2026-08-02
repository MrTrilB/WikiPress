<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class ListGroup
{
    public static function render(array $items, array $attributes = []): string
    {
        $itemsHtml = '';

        foreach ($items as $item) {
            if (is_array($item)) {
                $classes = array_merge(['list-group-item'], $item['classes'] ?? []);
                $itemsHtml .= Renderer::component('li', (string) ($item['label'] ?? ''), [
                    'classes' => $classes,
                    'attributes' => $item['attributes'] ?? [],
                ]);
            } else {
                $itemsHtml .= Renderer::component('li', (string) $item, [
                    'classes' => ['list-group-item'],
                ]);
            }
        }

        return Renderer::component('ul', $itemsHtml, [
            'classes' => ['list-group'],
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



