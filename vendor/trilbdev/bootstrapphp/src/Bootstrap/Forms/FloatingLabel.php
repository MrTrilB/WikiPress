<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class FloatingLabel
{
    public static function render(string $inputHtml, string $label, array $attributes = []): string
    {
        return Renderer::component('div', $inputHtml . Renderer::component('label', $label, [
            'attributes' => array_replace([
                'class' => 'form-label',
            ], $attributes['label_attributes'] ?? []),
            'escape' => true,
        ]), [
            'attributes' => array_replace([
                'class' => 'form-floating',
            ], $attributes['container_attributes'] ?? []),
            'escape' => false,
        ]);
    }
}



