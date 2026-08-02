<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class Flex
{
    public static function render(
        string $content,
        string|array $direction = 'row',
        bool $wrap = false,
        string|array|null $justify = null,
        string|array|null $align = null,
        string|array|null $gap = null,
        string|array|null $grow = null,
        string|array|null $shrink = null,
        array $attributes = []
    ): string {
        $classes = array_merge(
            ['d-flex'],
            self::buildResponsiveClasses('flex', $direction),
            $wrap ? ['flex-wrap'] : [],
            self::buildResponsiveClasses('justify-content', $justify),
            self::buildResponsiveClasses('align-items', $align),
            self::buildResponsiveClasses('gap', $gap),
            self::buildResponsiveClasses('flex-grow', $grow),
            self::buildResponsiveClasses('flex-shrink', $shrink)
        );

        return Renderer::component('div', $content, [
            'classes' => array_values(array_filter($classes)),
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }

    private static function buildResponsiveClasses(string $prefix, string|array|null $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            $classes = [];
            foreach ($value as $breakpoint => $option) {
                $breakpoint = trim((string) $breakpoint);
                $option = trim((string) $option);

                if ($breakpoint === '' || $breakpoint === 'default') {
                    $classes[] = sprintf('%s-%s', $prefix, $option);
                } else {
                    $classes[] = sprintf('%s-%s-%s', $prefix, $breakpoint, $option);
                }
            }

            return $classes;
        }

        return [sprintf('%s-%s', $prefix, trim((string) $value))];
    }
}



