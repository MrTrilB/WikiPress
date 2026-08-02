<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Nav
{
    public static function render(array $items, array $attributes = []): string
    {
        $itemsHtml = '';

        foreach ($items as $item) {
            $classes = ['nav-link'];
            $content = '';
            $href = '#';

            if (is_array($item)) {
                $content = (string) ($item['label'] ?? '');
                $href = $item['href'] ?? '#';
                if (!empty($item['active'])) {
                    $classes[] = 'active';
                }
            } else {
                $content = (string) $item;
            }

            $itemsHtml .= Renderer::component('li', Renderer::component('a', $content, [
                'classes' => $classes,
                'attributes' => ['href' => $href],
            ]), [
                'classes' => ['nav-item'],
                'escape' => false,
            ]);
        }

        return Renderer::component('ul', $itemsHtml, [
            'classes' => ['nav'],
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



