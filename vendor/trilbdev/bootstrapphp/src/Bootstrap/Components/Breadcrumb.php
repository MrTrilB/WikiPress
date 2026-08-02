<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Breadcrumb
{
    public static function render(array $items, array $attributes = []): string
    {
        $itemsHtml = '';

        foreach ($items as $item) {
            $classes = ['breadcrumb-item'];
            $content = '';

            if (is_array($item)) {
                $content = (string) ($item['label'] ?? '');
                $href = $item['href'] ?? null;
                if (!empty($item['active'])) {
                    $classes[] = 'active';
                }
                if ($href !== null && empty($item['active'])) {
                    $content = Renderer::component('a', $content, [
                        'attributes' => ['href' => $href],
                    ]);
                }
            } else {
                $content = (string) $item;
            }

            $itemsHtml .= Renderer::component('li', $content, [
                'classes' => $classes,
                'attributes' => is_array($item['active'] ?? null) ? [] : [],
                'escape' => false,
            ]);
        }

        return Renderer::component('nav', Renderer::component('ol', $itemsHtml, [
            'classes' => ['breadcrumb'],
        ]), [
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



