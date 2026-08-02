<?php

declare(strict_types=1);

namespace BootstrapPHP\Includes;

final class Renderer
{
    public static function component(string $tag, string $content = '', array $options = []): string
    {
        self::assertValidTag($tag);

        $attributes = is_array($options['attributes'] ?? null) ? $options['attributes'] : [];
        $classes = self::normalizeClasses($options['classes'] ?? []);

        if ($classes !== '') {
            $attributes['class'] = trim(($attributes['class'] ?? '') . ' ' . $classes);
        }

        $attributeString = self::renderAttributes($attributes);

        if ((bool) ($options['void'] ?? false)) {
            return sprintf('<%s%s>', $tag, $attributeString);
        }

        $body = (bool) ($options['escape'] ?? true)
            ? self::escape($content)
            : $content;

        return sprintf('<%s%s>%s</%s>', $tag, $attributeString, $body, $tag);
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function renderAttributes(array $attributes): string
    {
        $rendered = [];

        foreach ($attributes as $name => $value) {
            if (!is_string($name) || !preg_match('/^[a-zA-Z_:][a-zA-Z0-9_:\.-]*$/', $name)) {
                throw new \InvalidArgumentException(sprintf('Invalid HTML attribute name "%s".', (string) $name));
            }

            if ($value === false || $value === null) {
                continue;
            }

            if ($value === true) {
                $rendered[] = $name;
                continue;
            }

            $rendered[] = sprintf('%s="%s"', $name, self::escape((string) $value));
        }

        return $rendered === [] ? '' : ' ' . implode(' ', $rendered);
    }

    public static function normalizeClasses(string|array $classes): string
    {
        if (is_array($classes)) {
            $classList = $classes;
        } else {
            $trimmedClasses = trim($classes);
            $classList = $trimmedClasses === ''
                ? []
                : (preg_split('/\s+/', $trimmedClasses) ?: []);
        }

        $classList = array_values(array_unique(array_filter(array_map('strval', $classList))));

        return implode(' ', $classList);
    }

    private static function assertValidTag(string $tag): void
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9-]*$/', $tag)) {
            throw new \InvalidArgumentException(sprintf('Invalid HTML tag "%s".', $tag));
        }
    }
}
