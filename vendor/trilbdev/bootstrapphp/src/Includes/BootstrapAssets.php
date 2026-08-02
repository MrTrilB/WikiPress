<?php

declare(strict_types=1);

namespace BootstrapPHP\Includes;

use BootstrapPHP\Includes\AssetUrlBuilder;

class BootstrapAssets
{
    public function __construct(
        private readonly string $version = '5.3.3',
        private readonly string $assetBaseUrl = 'vendor/twbs/bootstrap/dist',
        private readonly string $cssPath = 'css/bootstrap.bundle.min.css',
        private readonly string $jsPath = 'js/bootstrap.bundle.min.js',
        private readonly ?string $package = null,
        private readonly ?string $type = null,
        private readonly ?string $build = null,
        private readonly bool $rtl = false,
        private readonly ?string $cssUrlOverride = null,
        private readonly ?string $jsUrlOverride = null,
        private readonly array $cssAttributes = [],
        private readonly array $jsAttributes = []
    ) {
    }

    public static function fromArray(array $config = []): self
    {
        $provided = $config['asset_base_url'] ?? null;

        if ($provided !== null && $provided !== '') {
            $assetBaseUrl = (string) $provided;
        } else {
            // Prefer project-level vendor path when available (developer workspace)
            $projectVendorDist = getcwd() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'twbs' . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'dist';

            if (is_dir($projectVendorDist)) {
                $assetBaseUrl = 'vendor/twbs/bootstrap/dist';
            } else {
                // When this package is itself installed under vendor, locate the vendor root
                $possibleVendorRoot = dirname(__DIR__, 4);
                $installedVendorDist = $possibleVendorRoot . DIRECTORY_SEPARATOR . 'twbs' . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'dist';

                if (is_dir($installedVendorDist)) {
                    $real = realpath($installedVendorDist);
                    $assetBaseUrl = $real !== false ? str_replace('\\', '/', $real) : $installedVendorDist;
                } else {
                    $assetBaseUrl = 'src/Assets/dist';
                }
            }
        }

        return new self(
            version: (string) ($config['version'] ?? '5.3.3'),
            assetBaseUrl: $assetBaseUrl,
            cssPath: (string) ($config['css_path'] ?? 'css/bootstrap.bundle.min.css'),
            jsPath: (string) ($config['js_path'] ?? 'js/bootstrap.bundle.min.js'),
            package: isset($config['package']) ? (string) $config['package'] : null,
            type: isset($config['type']) ? (string) $config['type'] : null,
            build: isset($config['build']) ? (string) $config['build'] : null,
            rtl: isset($config['rtl']) ? (bool) $config['rtl'] : false,
            cssUrlOverride: isset($config['css_url']) ? (string) $config['css_url'] : null,
            jsUrlOverride: isset($config['js_url']) ? (string) $config['js_url'] : null,
            cssAttributes: is_array($config['css_attributes'] ?? null) ? $config['css_attributes'] : [],
            jsAttributes: is_array($config['js_attributes'] ?? null) ? $config['js_attributes'] : []
        );
    }

    public function assetUrl(string $path): string
    {
        return AssetUrlBuilder::build($this->version, $path);
    }

    public function cssUrl(string $variant = 'bootstrap', bool $rtl = false, bool $minified = true): string
    {
        if ($this->cssUrlOverride !== null) {
            return $this->cssUrlOverride;
        }

        if ($variant === 'bootstrap' && !$rtl && $minified && $this->cssPath !== '') {
            return $this->assetUrl($this->cssPath);
        }

        return AssetUrlBuilder::css($this->version, $variant, $rtl, $minified);
    }

    public function jsUrl(string $variant = 'bundle', bool $minified = true): string
    {
        if ($this->jsUrlOverride !== null && $variant === 'bundle' && $minified) {
            return $this->jsUrlOverride;
        }

        if ($variant === 'bundle' && $minified && $this->jsPath !== '') {
            return $this->assetUrl($this->jsPath);
        }

        return AssetUrlBuilder::js($this->version, $variant, $minified);
    }

    public function cssAttributes(): array
    {
        return $this->cssAttributes;
    }

    public function jsAttributes(): array
    {
        return $this->jsAttributes;
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'asset_base_url' => $this->assetBaseUrl,
            'css_path' => $this->cssPath,
            'js_path' => $this->jsPath,
            'css_url' => $this->cssUrl(),
            'js_url' => $this->jsUrl(),
            'package' => $this->package,
            'type' => $this->type,
            'build' => $this->build,
            'rtl' => $this->rtl,
            'css_attributes' => $this->cssAttributes,
            'js_attributes' => $this->jsAttributes,
        ];
    }

    public function package(): ?string
    {
        return $this->package;
    }

    public function type(): ?string
    {
        return $this->type;
    }

    public function build(): ?string
    {
        return $this->build;
    }

    public function rtl(): bool
    {
        return $this->rtl;
    }
}

