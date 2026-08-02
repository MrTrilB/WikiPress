<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class InputGroup
{
    public static function render(array $items, array $attributes = []): string
    {
        $content = '';

        foreach ($items as $item) {
            if (is_string($item)) {
                $content .= Renderer::component('span', $item, [
                    'classes' => ['input-group-text'],
                    'escape' => true,
                ]);
                continue;
            }

            if (is_array($item)) {
                $tag = $item['tag'] ?? 'span';
                $itemAttributes = $item['attributes'] ?? [];
                $itemClasses = $item['classes'] ?? [];
                $itemContent = $item['content'] ?? '';
                $content .= Renderer::component($tag, (string) $itemContent, [
                    'attributes' => $itemAttributes,
                    'classes' => array_merge(['input-group-text'], (array) $itemClasses),
                    'escape' => $item['escape'] ?? true,
                ]);
            }
        }

        return Renderer::component('div', $content, [
            'classes' => array_merge(['input-group'], (array) ($attributes['classes'] ?? [])),
            'attributes' => array_replace($attributes, ['classes' => null]),
            'escape' => false,
        ]);
    }
}



