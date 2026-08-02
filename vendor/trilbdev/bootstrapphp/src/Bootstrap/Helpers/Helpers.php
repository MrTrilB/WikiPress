<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Helpers;

final class Helpers
{
    public static function clearfix(string $content, array $attributes = [], bool $escape = true): string
    {
        return Clearfix::render($content, $attributes, $escape);
    }

    public static function coloredLink(string $content, string $color = 'primary', array $attributes = [], bool $escape = true): string
    {
        return ColoredLink::render($content, $color, $attributes, $escape);
    }

    public static function ratio(string $content, string $ratio = '16x9', array $attributes = [], bool $escape = true): string
    {
        return Ratio::render($content, $ratio, $attributes, $escape);
    }

    public static function position(string $content, string $position = 'relative', array $attributes = [], bool $escape = true): string
    {
        return Position::render($content, $position, $attributes, $escape);
    }

    public static function stack(string $content, string $direction = 'vertical', string $gap = '3', array $attributes = [], bool $escape = false): string
    {
        return StackHelper::render($content, $direction, $gap, $attributes, $escape);
    }

    public static function stretchedLink(string $content, string $href = '#', array $attributes = [], bool $escape = true): string
    {
        return StretchedLink::render($content, $href, $attributes, $escape);
    }

    public static function textTruncation(string $content, array $attributes = [], bool $escape = true): string
    {
        return TextTruncation::render($content, $attributes, $escape);
    }

    public static function verticalRule(array $attributes = []): string
    {
        return VerticalRule::render($attributes);
    }

    public static function visuallyHidden(string $content, array $attributes = [], bool $escape = true): string
    {
        return VisuallyHidden::render($content, $attributes, $escape);
    }
}

