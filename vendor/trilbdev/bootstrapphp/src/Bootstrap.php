<?php

declare(strict_types=1);

namespace BootstrapPHP;

use BootstrapPHP\Bootstrap\Components\Components;
use BootstrapPHP\Bootstrap\Content\Content;
use BootstrapPHP\Bootstrap\Forms\Forms;
use BootstrapPHP\Bootstrap\Helpers\Helpers;
use BootstrapPHP\Bootstrap\Layout\Layout;
use BootstrapPHP\Bootstrap\Utilities\Utilities as BootstrapUtilities;
use BootstrapPHP\Includes\BootstrapAssets;
use BootstrapPHP\Includes\Renderer;

final class Bootstrap
{
    public static function config(array $options = []): BootstrapAssets
    {
        return BootstrapAssets::fromArray($options);
    }

    public static function cssTag(?BootstrapAssets $config = null, string $variant = 'bootstrap', bool $rtl = false, bool $minified = true, array $attributes = []): string
    {
        $config ??= new BootstrapAssets();

        return self::component('link', '', [
            'attributes' => array_replace(
                [
                    'rel' => 'stylesheet',
                    'href' => $config->cssUrl($variant, $rtl, $minified),
                ],
                $config->cssAttributes(),
                $attributes
            ),
            'void' => true,
        ]);
    }

    public static function jsTag(?BootstrapAssets $config = null, string $variant = 'bundle', bool $minified = true, array $attributes = []): string
    {
        $config ??= new BootstrapAssets();

        return self::component('script', '', [
            'attributes' => array_replace(
                [
                    'src' => $config->jsUrl($variant, $minified),
                ],
                $config->jsAttributes(),
                $attributes
            ),
            'escape' => false,
        ]);
    }

    public static function assets(?BootstrapAssets $config = null, string $separator = PHP_EOL): string
    {
        $config ??= new BootstrapAssets();

        $package = $config->package();

        // If package specified, return only that package's tag
        if ($package === 'css') {
            $variant = $config->type() ?? 'bootstrap';
            $minified = ($config->build() ?? 'min') === 'min';
            $rtl = $config->rtl();

            return self::cssTag($config, $variant, $rtl, $minified);
        }

        if ($package === 'js') {
            $variant = $config->type() ?? 'bundle';
            $minified = ($config->build() ?? 'min') === 'min';

            return self::jsTag($config, $variant, $minified);
        }

        return self::cssTag($config) . $separator . self::jsTag($config);
    }

    public static function assetsUrls(?BootstrapAssets $config = null): array
    {
        $config ??= new BootstrapAssets();

        return [
            'css' => $config->cssUrl(),
            'js' => $config->jsUrl(),
        ];
    }

    public static function cssUrl(?BootstrapAssets $config = null, string $variant = 'bootstrap', bool $rtl = false, bool $minified = true): string
    {
        return ($config ?? new BootstrapAssets())->cssUrl($variant, $rtl, $minified);
    }

    public static function jsUrl(?BootstrapAssets $config = null, string $variant = 'bundle', bool $minified = true): string
    {
        return ($config ?? new BootstrapAssets())->jsUrl($variant, $minified);
    }

    public static function assetUrl(string $path, ?BootstrapAssets $config = null): string
    {
        return ($config ?? new BootstrapAssets())->assetUrl($path);
    }

    public static function component(string $tag, string $content = '', array $options = []): string
    {
        return Renderer::component($tag, $content, $options);
    }

    public static function content(): Content
    {
        return new Content();
    }

    public static function components(): Components
    {
        return new Components();
    }

    public static function forms(): Forms
    {
        return new Forms();
    }

    public static function helpers(): Helpers
    {
        return new Helpers();
    }

    public static function layout(): Layout
    {
        return new Layout();
    }

    public static function utilities(): BootstrapUtilities
    {
        return new BootstrapUtilities();
    }
}



