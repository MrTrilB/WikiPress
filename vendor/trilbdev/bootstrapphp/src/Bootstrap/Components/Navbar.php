<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;

final class Navbar
{
    public static function render(string $brand, array $items, array $attributes = []): string
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

        $nav = Renderer::component('div', Renderer::component('a', $brand, [
            'classes' => ['navbar-brand'],
            'attributes' => ['href' => '#'],
        ]) . Renderer::component('div', Renderer::component('ul', $itemsHtml, [
            'classes' => ['navbar-nav', 'me-auto', 'mb-2', 'mb-lg-0'],
            'escape' => false,
        ]), [
            'classes' => ['collapse', 'navbar-collapse'],
            'escape' => false,
        ]), [
            'classes' => ['container-fluid'],
            'escape' => false,
        ]);

        return Renderer::component('nav', $nav, [
            'classes' => ['navbar', 'navbar-expand-lg', 'navbar-light', 'bg-light'],
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



