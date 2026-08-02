<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

final class Content
{
    public static function address(string $content, array $attributes = [], bool $escape = true): string
    {
        return Address::render($content, $attributes, $escape);
    }

    public static function blockquote(string $content, array $attributes = [], bool $escape = true): string
    {
        return Quote::render($content, $attributes, false, $escape);
    }

    public static function codeBlock(string $content, array $attributes = [], bool $escape = true): string
    {
        return CodeBlock::render($content, $attributes, $escape);
    }

    public static function figure(string $content, array $attributes = [], bool $escape = false): string
    {
        return Figure::render($content, $attributes);
    }

    public static function figureCaption(string $content, array $attributes = [], bool $escape = true): string
    {
        return FigureCaption::render($content, $attributes, $escape);
    }

    public static function heading(string $content, int $level = 1, array $attributes = []): string
    {
        return Heading::render($content, $level, $attributes);
    }

    public static function image(string $src, string $alt = '', array $attributes = []): string
    {
        return Image::render($src, $alt, $attributes);
    }

    public static function listGroup(array $items, array $attributes = [], bool $flush = false): string
    {
        return ListGroup::render($items, $attributes, $flush);
    }

    public static function paragraph(string $content, array $attributes = [], bool $escape = true): string
    {
        return Paragraph::render($content, $attributes, $escape);
    }

    public static function pre(string $content, array $attributes = [], bool $escape = true): string
    {
        return Pre::render($content, $attributes, $escape);
    }

    public static function quote(string $content, array $attributes = [], bool $reverse = false, bool $escape = true): string
    {
        return Quote::render($content, $attributes, $reverse, $escape);
    }

    public static function table(array $headers, array $rows, array $attributes = [], bool $striped = false, bool $bordered = false): string
    {
        return Table::render($headers, $rows, $attributes, $striped, $bordered);
    }

    public static function textMuted(string $content, array $attributes = [], bool $escape = true): string
    {
        return TextMuted::render($content, $attributes, $escape);
    }
}

