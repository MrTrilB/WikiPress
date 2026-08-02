<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Pagination
{
    public static function render(array $pages, array $attributes = []): string
    {
        $itemsHtml = '';

        foreach ($pages as $page) {
            $classes = ['page-item'];
            $content = '';
            $href = '#';

            if (is_array($page)) {
                $content = (string) ($page['label'] ?? '');
                $href = $page['href'] ?? '#';
                if (!empty($page['active'])) {
                    $classes[] = 'active';
                }
                if (!empty($page['disabled'])) {
                    $classes[] = 'disabled';
                }
            } else {
                $content = (string) $page;
            }

            $itemsHtml .= Renderer::component('li', Renderer::component('a', $content, [
                'classes' => ['page-link'],
                'attributes' => ['href' => $href],
            ]), [
                'classes' => $classes,
                'escape' => false,
            ]);
        }

        return Renderer::component('nav', Renderer::component('ul', $itemsHtml, [
            'classes' => ['pagination'],
            'attributes' => $attributes,
            'escape' => false,
        ]), ['escape' => false]);
    }
}



