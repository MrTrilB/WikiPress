<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Components;

final class Components
{
    public static function accordion(array $items, string $id = 'accordionExample', array $attributes = []): string
    {
        return Accordion::render($items, $id, $attributes);
    }

    public static function alert(string $content, string $variant = 'primary', array $attributes = []): string
    {
        return Alert::render($content, $variant, $attributes);
    }

    public static function badge(string $content, string $variant = 'primary', array $attributes = []): string
    {
        return Badge::render($content, $variant, $attributes);
    }

    public static function breadcrumb(array $items, array $attributes = []): string
    {
        return Breadcrumb::render($items, $attributes);
    }

    public static function button(string $content, string $variant = 'primary', array $attributes = []): string
    {
        return Button::render($content, $variant, $attributes);
    }

    public static function buttonGroup(array $buttons, array $attributes = [], bool $vertical = false): string
    {
        return ButtonGroup::render($buttons, $attributes, $vertical);
    }

    public static function card(string $body, array $options = []): string
    {
        return Card::render($body, $options);
    }

    public static function carousel(array $items, string $id = 'carouselExample', array $attributes = []): string
    {
        return Carousel::render($items, $id, $attributes);
    }

    public static function closeButton(array $attributes = []): string
    {
        return Button::render('', 'close', array_replace(['type' => 'button', 'aria-label' => 'Close'], $attributes));
    }

    public static function collapse(string $content, string $id, bool $show = false, array $attributes = []): string
    {
        return Collapse::render($content, $id, $show, $attributes);
    }

    public static function dropdown(string $buttonLabel, array $items, string $id = 'dropdownMenu', array $attributes = []): string
    {
        return Dropdown::render($buttonLabel, $items, $id, $attributes);
    }

    public static function listGroup(array $items, array $attributes = []): string
    {
        return ListGroup::render($items, $attributes);
    }

    public static function modal(string $title, string $body, string $id = 'modalExample', array $options = []): string
    {
        return Modal::render($title, $body, $id, $options);
    }

    public static function nav(array $items, array $attributes = []): string
    {
        return Nav::render($items, $attributes);
    }

    public static function navbar(string $brand, array $items, array $attributes = []): string
    {
        return Navbar::render($brand, $items, $attributes);
    }

    public static function offcanvas(string $title, string $body, string $id = 'offcanvasExample', array $options = []): string
    {
        return Offcanvas::render($title, $body, $id, $options);
    }

    public static function pagination(array $pages, array $attributes = []): string
    {
        return Pagination::render($pages, $attributes);
    }

    public static function placeholder(string $content = '', array $attributes = []): string
    {
        return Placeholder::render($content, $attributes);
    }

    public static function popover(string $title, string $content, string $id = 'popoverExample', array $attributes = []): string
    {
        return Popover::render($title, $content, $id, $attributes);
    }

    public static function progress(int $value, int $max = 100, string $variant = 'primary', array $attributes = []): string
    {
        return Progress::render($value, $max, $variant, $attributes);
    }

    public static function spinner(string $variant = 'primary', array $attributes = []): string
    {
        return Spinner::render($variant, $attributes);
    }

    public static function toast(string $title, string $body, array $options = []): string
    {
        return Toast::render($title, $body, $options);
    }

    public static function tooltip(string $label, string $title, string $id = 'tooltipExample', array $attributes = []): string
    {
        return Tooltip::render($label, $title, $id, $attributes);
    }
}

