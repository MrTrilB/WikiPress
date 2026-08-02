<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Utilities;

use BootstrapPHP\Includes\Renderer;
use BootstrapPHP\Bootstrap\Utilities\Api;
use BootstrapPHP\Bootstrap\Utilities\Background;
use BootstrapPHP\Bootstrap\Utilities\Border;
use BootstrapPHP\Bootstrap\Utilities\Colors;
use BootstrapPHP\Bootstrap\Utilities\Display;
use BootstrapPHP\Bootstrap\Utilities\FloatHelper;
use BootstrapPHP\Bootstrap\Utilities\Flex;
use BootstrapPHP\Bootstrap\Utilities\Interactions;
use BootstrapPHP\Bootstrap\Utilities\Link;
use BootstrapPHP\Bootstrap\Utilities\ObjectFit;
use BootstrapPHP\Bootstrap\Utilities\Opacity;
use BootstrapPHP\Bootstrap\Utilities\Overflow;
use BootstrapPHP\Bootstrap\Helpers\Position;
use BootstrapPHP\Bootstrap\Utilities\Shadows;
use BootstrapPHP\Bootstrap\Utilities\Sizing;
use BootstrapPHP\Bootstrap\Utilities\Spacing;
use BootstrapPHP\Bootstrap\Utilities\Text;
use BootstrapPHP\Bootstrap\Utilities\VerticalAlign;
use BootstrapPHP\Bootstrap\Utilities\Visibility;

final class Utilities
{
    public static function api(string|array $classes): string
    {
        return Api::classNames($classes);
    }

    public static function classNames(string|array $classes): string
    {
        return Renderer::normalizeClasses($classes);
    }

    public static function background(string $content, string $variant = 'primary', array $attributes = [], bool $escape = true): string
    {
        return Background::render($content, $variant, $attributes, $escape);
    }

    public static function border(string $content, ?string $variant = null, bool $rounded = false, array $attributes = [], bool $escape = true): string
    {
        return Border::render($content, $variant, $rounded, $attributes, $escape);
    }

    public static function colors(string $content, string $variant = 'primary', string $mode = 'text', array $attributes = [], bool $escape = true): string
    {
        return Colors::render($content, $variant, $mode, $attributes, $escape);
    }

    public static function display(string $content, string $display = 'block', array $attributes = [], bool $escape = true): string
    {
        return Display::render($content, $display, $attributes, $escape);
    }

    public static function flex(string $content, string|array $direction = 'row', bool $wrap = false, ?string $justify = null, ?string $align = null, ?string $gap = null, array $attributes = []): string
    {
        return Flex::render($content, $direction, $wrap, $justify, $align, $gap, $attributes);
    }

    public static function float(string $content, string|array $direction = 'start', array $attributes = [], bool $escape = true): string
    {
        return FloatHelper::render($content, $direction, $attributes, $escape);
    }

    public static function interaction(string $content, string $kind = 'pointer', string $value = 'auto', array $attributes = [], bool $escape = true): string
    {
        return Interactions::render($content, $kind, $value, $attributes, $escape);
    }

    public static function link(string $content, string $variant = 'primary', array $attributes = [], bool $escape = true): string
    {
        return Link::render($content, $variant, $attributes, $escape);
    }

    public static function objectFit(string $content, string $fit = 'cover', array $attributes = [], bool $escape = true): string
    {
        return ObjectFit::render($content, $fit, $attributes, $escape);
    }

    public static function opacity(string $content, string $level = '100', array $attributes = [], bool $escape = true): string
    {
        return Opacity::render($content, $level, $attributes, $escape);
    }

    public static function overflow(string $content, string $axis = 'auto', array $attributes = [], bool $escape = true): string
    {
        return Overflow::render($content, $axis, $attributes, $escape);
    }


    public static function shadows(string $content, string $level = '1', array $attributes = [], bool $escape = true): string
    {
        return Shadows::render($content, $level, $attributes, $escape);
    }

    public static function sizing(string $content, string $property = 'w', string $size = '100', array $attributes = [], bool $escape = true): string
    {
        return Sizing::render($content, $property, $size, $attributes, $escape);
    }

    public static function spacing(string $content, string $type = 'm', string $position = '', string $size = '3', array $attributes = [], bool $escape = true): string
    {
        return Spacing::render($content, $type, $position, $size, $attributes, $escape);
    }

    public static function text(string $content, ?string $alignment = null, ?string $color = null, array $attributes = [], bool $escape = true): string
    {
        return Text::render($content, $alignment, $color, $attributes, $escape);
    }

    public static function verticalAlign(string $content, string $alignment = 'middle', array $attributes = [], bool $escape = true): string
    {
        return VerticalAlign::render($content, $alignment, $attributes, $escape);
    }

    public static function visibility(string $content, string $state = 'visible', array $attributes = [], bool $escape = true): string
    {
        return Visibility::render($content, $state, $attributes, $escape);
    }
}



