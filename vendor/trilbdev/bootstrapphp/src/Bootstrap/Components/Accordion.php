<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

use BootstrapPHP\Includes\Renderer;
use InvalidArgumentException;

final class Accordion
{
    public static function render(array $items, string $id = 'accordionExample', array $attributes = []): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('Accordion ID cannot be empty.');
        }

        $accordionItems = [];

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException(sprintf('Accordion item at index %d must be an array.', $index));
            }

            $itemId = (string) ($item['id'] ?? sprintf('%s-item-%d', $id, $index));
            $collapseId = (string) ($item['collapse_id'] ?? sprintf('%s-collapse-%d', $id, $index));
            $title = (string) ($item['title'] ?? '');
            $content = (string) ($item['content'] ?? '');
            $opened = (bool) ($item['opened'] ?? false);

            $buttonAttributes = array_replace([
                'type' => 'button',
                'data-bs-toggle' => 'collapse',
                'data-bs-target' => '#' . $collapseId,
                'aria-expanded' => $opened ? 'true' : 'false',
                'aria-controls' => $collapseId,
            ], is_array($item['button_attributes'] ?? null) ? $item['button_attributes'] : []);

            $collapseAttributes = array_replace([
                'id' => $collapseId,
                'class' => $opened ? 'accordion-collapse collapse show' : 'accordion-collapse collapse',
                'aria-labelledby' => $itemId,
                'data-bs-parent' => '#' . $id,
            ], is_array($item['collapse_attributes'] ?? null) ? $item['collapse_attributes'] : []);

            $header = Renderer::component('h2', Renderer::component('button', $title, [
                'classes' => ['accordion-button', $opened ? '' : 'collapsed'],
                'attributes' => $buttonAttributes,
            ]), [
                'classes' => ['accordion-header'],
                'attributes' => array_replace(['id' => $itemId], is_array($item['header_attributes'] ?? null) ? $item['header_attributes'] : []),
            ]);

            $body = Renderer::component('div', $content, [
                'classes' => ['accordion-body'],
                'attributes' => is_array($item['body_attributes'] ?? null) ? $item['body_attributes'] : [],
                'escape' => (bool) ($item['escape'] ?? true),
            ]);

            $collapse = Renderer::component('div', $body, [
                'classes' => [],
                'attributes' => $collapseAttributes,
                'escape' => false,
            ]);

            $accordionItems[] = Renderer::component('div', $header . $collapse, [
                'classes' => ['accordion-item'],
                'attributes' => is_array($item['item_attributes'] ?? null) ? $item['item_attributes'] : [],
                'escape' => false,
            ]);
        }

        return Renderer::component('div', implode('', $accordionItems), [
            'classes' => ['accordion'],
            'attributes' => array_replace(['id' => $id], $attributes),
            'escape' => false,
        ]);
    }
}



