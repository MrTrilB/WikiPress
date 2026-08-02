<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

use BootstrapPHP\Includes\Renderer;

final class Col
{
    public static function render(string $content, string|array $classes = 'col', array $attributes = [], bool $escape = true, array $options = []): string
    {
        $classes = array_merge(self::normalizeClasses($classes), self::buildOptionsClasses($options));

        return Renderer::component('div', $content, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => $escape,
        ]);
    }

    private static function normalizeClasses(string|array $classes): array
    {
        if (is_array($classes)) {
            return array_values(array_filter(array_map('strval', $classes)));
        }

        return preg_split('/\s+/', trim($classes)) ?: [];
    }

    private static function buildOptionsClasses(array $options): array
    {
        $classes = [];

        if (isset($options['size'])) {
            $classes = array_merge($classes, self::buildResponsiveSizeClasses($options['size']));
        }

        if (!empty($options['offset'])) {
            $classes = array_merge($classes, self::buildResponsiveSizeClasses($options['offset'], 'offset'));
        }

        if (!empty($options['order'])) {
            $classes = array_merge($classes, self::buildResponsiveSizeClasses($options['order'], 'order'));
        }

        if (!empty($options['auto'])) {
            $classes[] = 'col-auto';
        }

        if (!empty($options['alignSelf'])) {
            $classes[] = sprintf('align-self-%s', $options['alignSelf']);
        }

        return $classes;
    }

    private static function buildResponsiveSizeClasses(string|int|array $value, string $prefix = 'col'): array
    {
        $classes = [];

        if (is_array($value)) {
            foreach ($value as $breakpoint => $size) {
                $breakpoint = trim((string) $breakpoint);
                $size = trim((string) $size);

                if ($breakpoint === '' || $breakpoint === 'default') {
                    $classes[] = $prefix . '-' . $size;
                    continue;
                }

                $classes[] = sprintf('%s-%s-%s', $prefix, $breakpoint, $size);
            }

            return $classes;
        }

        return [sprintf('%s-%s', $prefix, trim((string) $value))];
    }
}



