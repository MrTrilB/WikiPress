<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class Radio
{
    public static function render(string $name, string $value = '', bool $checked = false, string $label = '', array $attributes = []): string
    {
        $attributes = array_replace([
            'type' => 'radio',
            'name' => $name,
            'value' => $value,
            'class' => 'form-check-input',
        ], $attributes);

        if ($checked) {
            $attributes['checked'] = true;
        }

        $input = Renderer::component('input', '', [
            'attributes' => $attributes,
            'void' => true,
        ]);

        $labelHtml = '';
        if ($label !== '') {
            $labelHtml = Renderer::component('label', $label, [
                'attributes' => array_replace([
                    'class' => 'form-check-label',
                ], $attributes['id'] ? ['for' => $attributes['id']] : []),
                'escape' => true,
            ]);
        }

        return Renderer::component('div', $input . $labelHtml, [
            'classes' => ['form-check'],
            'escape' => false,
        ]);
    }
}



