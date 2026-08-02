<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class ListGroup
{
    public static function render(array $items, array $attributes = [], bool $flush = false): string
    {
        $classes = ['list-group'];
        if ($flush) {
            $classes[] = 'list-group-flush';
        }

        $content = '';
        foreach ($items as $item) {
            $content .= Renderer::component('li', (string) $item, [
                'classes' => ['list-group-item'],
                'escape' => true,
            ]);
        }

        return Renderer::component('ul', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



