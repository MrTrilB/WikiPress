<?php

declare(strict_types=1);

namespace BootstrapPHP\Includes;

final class AssetUrlBuilder
{
    private const ASSET_BASE = '/vendor/twbs/bootstrap/dist';

    public static function build(string $version, string $path): string
    {
        // The asset base is intentionally fixed here (project vendor dist).
        $assetBaseUrl = self::ASSET_BASE;
        $baseUrl = rtrim(str_replace('{version}', $version, $assetBaseUrl), '/');
        $assetPath = ltrim($path, '/');

        if ($baseUrl === '' && $assetPath === '') {
            return '';
        }

        if ($baseUrl === '') {
            return $assetPath;
        }

        if ($assetPath === '') {
            return $baseUrl;
        }

        return $baseUrl . '/' . $assetPath;
    }

    public static function css(string $version, string $variant = 'bootstrap', bool $rtl = false, bool $minified = true): string
    {
        $v = strtolower($variant);
        $name = match ($v) {
            '', 'bootstrap', 'none' => 'bootstrap',
            'grid' => 'bootstrap-grid',
            'reboot' => 'bootstrap-reboot',
            'utilities' => 'bootstrap-utilities',
            default => $variant,
        };

        $path = sprintf('css/%s%s%s.css', $name, $rtl ? '.rtl' : '', $minified ? '.min' : '');

        return self::build($version, $path);
    }

    public static function js(string $version, string $variant = 'bundle', bool $minified = true): string
    {
        $v = strtolower($variant);
        $name = match ($v) {
            '', 'bootstrap', 'none' => 'bootstrap',
            'bundle' => 'bootstrap.bundle',
            'esm' => 'bootstrap.esm',
            default => $variant,
        };

        $path = sprintf('js/%s%s.js', $name, $minified ? '.min' : '');

        return self::build($version, $path);
    }
}
