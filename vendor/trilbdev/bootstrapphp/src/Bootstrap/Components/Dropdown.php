<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Dropdown
{
    public static function render(string $buttonLabel, array $items, string $id = 'dropdownMenu', array $attributes = []): string
    {
        $itemHtml = '';

        foreach ($items as $item) {
            if (is_array($item) && isset($item['href'])) {
                $itemHtml .= Renderer::component('a', (string) ($item['label'] ?? ''), [
                    'classes' => ['dropdown-item'],
                    'attributes' => array_replace(['href' => (string) $item['href']], $item['attributes'] ?? []),
                ]);
                continue;
            }

            $itemHtml .= Renderer::component('div', (string) $item, [
                'classes' => ['dropdown-item-text'],
                'escape' => true,
            ]);
        }

        $button = Renderer::component('button', $buttonLabel, [
            'classes' => ['btn', 'btn-secondary', 'dropdown-toggle'],
            'attributes' => array_replace([
                'type' => 'button',
                'data-bs-toggle' => 'dropdown',
                'aria-expanded' => 'false',
                'id' => $id,
            ], $attributes),
        ]);

        $menu = Renderer::component('ul', $itemHtml, [
            'classes' => ['dropdown-menu'],
            'attributes' => ['aria-labelledby' => $id],
            'escape' => false,
        ]);

        return Renderer::component('div', $button . $menu, [
            'classes' => ['dropdown'],
            'escape' => false,
        ]);
    }
}



