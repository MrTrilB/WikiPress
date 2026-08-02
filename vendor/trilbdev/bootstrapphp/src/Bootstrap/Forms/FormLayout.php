<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class FormLayout
{
    public static function render(string $content, bool $inline = false, ?string $gutter = '3', ?string $align = null, ?string $justify = null, array $attributes = []): string
    {
        $classes = [];

        if ($inline) {
            $classes[] = 'row row-cols-lg-auto g-' . trim((string) ($gutter ?? '3'));
            $classes[] = 'align-items-center';
        }

        if ($align !== null && $align !== '') {
            $classes[] = sprintf('align-items-%s', trim($align));
        }

        if ($justify !== null && $justify !== '') {
            $classes[] = sprintf('justify-content-%s', trim($justify));
        }

        if (!$inline) {
            $classes[] = 'row';
            if ($gutter !== null && $gutter !== '') {
                $classes[] = 'g-' . trim($gutter);
            }
        }

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



