<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;
use InvalidArgumentException;

final class Carousel
{
    public static function render(array $items, string $id = 'carouselExample', array $attributes = []): string
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Carousel items cannot be empty.');
        }

        $indicators = '';
        $inner = '';

        foreach ($items as $index => $item) {
            $active = $index === 0;
            $indicators .= Renderer::component('button', '', [
                'attributes' => [
                    'type' => 'button',
                    'data-bs-target' => '#' . $id,
                    'data-bs-slide-to' => (string) $index,
                    'aria-label' => 'Slide ' . ($index + 1),
                    'class' => $active ? 'active' : null,
                    'aria-current' => $active ? 'true' : null,
                ],
            ]);

            $inner .= Renderer::component('div', Renderer::component('img', '', [
                'attributes' => [
                    'src' => (string) ($item['src'] ?? ''),
                    'class' => 'd-block w-100',
                    'alt' => (string) ($item['alt'] ?? ''),
                ],
                'void' => true,
            ]), [
                'classes' => ['carousel-item', $active ? 'active' : ''],
                'escape' => false,
            ]);
        }

        $inner = Renderer::component('div', $inner, [
            'classes' => ['carousel-inner'],
            'escape' => false,
        ]);

        return Renderer::component('div', Renderer::component('div', $indicators, [
            'classes' => ['carousel-indicators'],
            'escape' => false,
        ]) . $inner, [
            'classes' => ['carousel', 'slide'],
            'attributes' => array_replace(['id' => $id, 'data-bs-ride' => 'carousel'], $attributes),
            'escape' => false,
        ]);
    }
}



