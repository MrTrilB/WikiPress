<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Layout;

final class Layout
{
    public static function container(string $content, string|bool $size = false, array $attributes = [], bool $escape = true): string
    {
        return Container::render($content, $size, $attributes, $escape);
    }

    public static function row(
        string $content,
        array $attributes = [],
        ?string $gutter = null,
        ?string $gutterX = null,
        ?string $gutterY = null,
        ?string $align = null,
        ?string $justify = null
    ): string {
        return Row::render($content, $attributes, $gutter, $gutterX, $gutterY, $align, $justify);
    }

    public static function col(string $content, string|array $classes = 'col', array $attributes = [], bool $escape = true, array $options = []): string
    {
        return Col::render($content, $classes, $attributes, $escape, $options);
    }

    public static function flex(
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
        return Flex::render($content, $direction, $wrap, $justify, $align, $gap, $grow, $shrink, $attributes);
    }

    public static function grid(string $content, string|int|array $columns = 12, ?string $gutter = null, array $attributes = []): string
    {
        return Grid::render($content, $columns, $gutter, $attributes);
    }

    public static function stack(string $content, string|array $gap = '3', array $attributes = []): string
    {
        return Stack::render($content, $gap, $attributes);
    }

    public static function cssGrid(string $content, string|array|null $columns = null, ?string $gap = null, array $attributes = [], bool $escape = false): string
    {
        return CssGrid::render($content, $columns, $gap, $attributes, $escape);
    }

    public static function zIndex(string $content, string|int $level = 'auto', ?string $position = null, array $attributes = [], bool $escape = true): string
    {
        return ZIndex::render($content, $level, $position, $attributes, $escape);
    }
}

