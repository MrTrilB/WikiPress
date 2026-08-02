<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Forms;

final class Forms
{
    public function input(string $name, string $value = '', array $attributes = []): string
    {
        return Input::render($name, $value, $attributes);
    }

    public function textarea(string $name, string $content = '', array $attributes = []): string
    {
        return Textarea::render($name, $content, $attributes);
    }

    public function select(string $name, array $options, ?string $selected = null, array $attributes = []): string
    {
        return Select::render($name, $options, $selected, $attributes);
    }

    public function checkbox(string $name, string $value = '1', bool $checked = false, string $label = '', array $attributes = []): string
    {
        return Checkbox::render($name, $value, $checked, $label, $attributes);
    }

    public function radio(string $name, string $value = '', bool $checked = false, string $label = '', array $attributes = []): string
    {
        return Radio::render($name, $value, $checked, $label, $attributes);
    }

    public function fileInput(string $name, array $attributes = []): string
    {
        return FileInput::render($name, $attributes);
    }

    public function range(string $name, int $value = 0, int $min = 0, int $max = 100, int $step = 1, array $attributes = []): string
    {
        return Range::render($name, $value, $min, $max, $step, $attributes);
    }

    public function label(string $content, array $attributes = []): string
    {
        return Label::render($content, $attributes);
    }

    public function formGroup(string $content, array $attributes = []): string
    {
        return FormGroup::render($content, $attributes);
    }

    public function floatingLabel(string $inputHtml, string $label, array $attributes = []): string
    {
        return FloatingLabel::render($inputHtml, $label, $attributes);
    }

    public function inputGroup(array $items, array $attributes = []): string
    {
        return InputGroup::render($items, $attributes);
    }

    public function validationFeedback(string $message, bool $valid = false, array $attributes = [], bool $escape = true): string
    {
        return ValidationFeedback::render($message, $valid, $attributes, $escape);
    }

    public function formText(string $content, array $attributes = [], bool $escape = true): string
    {
        return FormText::render($content, $attributes, $escape);
    }

    public function layout(string $content, bool $inline = false, ?string $gutter = '3', ?string $align = null, ?string $justify = null, array $attributes = []): string
    {
        return FormLayout::render($content, $inline, $gutter, $align, $justify, $attributes);
    }
}

