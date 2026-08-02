<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

use BootstrapPHP\Includes\Renderer;

final class Select
{
    public static function render(string $name, array $options, ?string $selected = null, array $attributes = []): string
    {
        $optionsHtml = '';

        foreach ($options as $value => $label) {
            $optionAttributes = ['value' => (string) $value];

            if ($selected !== null && (string) $value === (string) $selected) {
                $optionAttributes['selected'] = true;
            }

            $optionsHtml .= Renderer::component('option', (string) $label, [
                'attributes' => $optionAttributes,
            ]);
        }

        return Renderer::component('select', $optionsHtml, [
            'attributes' => array_replace(['name' => $name], $attributes),
            'escape' => false,
        ]);
    }
}



