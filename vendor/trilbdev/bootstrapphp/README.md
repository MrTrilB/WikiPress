 # BootstrapPHP

 Lightweight PHP helpers for rendering Bootstrap assets and components.

 This package expects Bootstrap to be provided as a Composer dependency (`twbs/bootstrap`) and resolves assets from a fixed internal base: `/vendor/twbs/bootstrap/dist`.
 To override specific outputs prefer `css_url` or `js_url` in your config; the internal builder base is fixed by design.

 **Quick goals:**
 - Provide simple tag helpers for Bootstrap CSS/JS
 - Offer class-based component renderers (alerts, buttons, containers, etc.)
 - Resolve assets from the vendor path; override specific URLs via `css_url` / `js_url`

 **Installation**

 Install this package and Bootstrap via Composer:

 ```bash
 composer require trilbdev/bootstrapphp
 composer require twbs/bootstrap
 ```

 **Basic usage**

 ```php
 <?php
 require __DIR__ . '/vendor/autoload.php';

 use BootstrapPHP\Bootstrap;

 // Output CSS and JS tags (defaults to vendor/twbs/bootstrap/dist)
 echo Bootstrap::assets();

 // Render a simple alert
 echo Bootstrap::alert('Saved successfully', 'success');

 // Generic component
 echo Bootstrap::component('div', 'Card content', [
     'classes' => ['card', 'p-3'],
 ]);
 ```

 **Configuration**

 The package resolves assets against the fixed base `/vendor/twbs/bootstrap/dist`. To override settings, pass a config array to `Bootstrap::config()` or `BootstrapAssets::fromArray()`. Note: to change the final CSS/JS URLs, set `css_url` or `js_url` in the config.

 Example config options:

 - `version` — Bootstrap version string (informational; used when building paths containing `{version}`)
 - `asset_base_url` — (legacy) path or URL to the Bootstrap `dist` directory. The internal builder resolves against the fixed base; use `css_url`/`js_url` to override outputs.
 - `css_path` / `js_path` — specific files inside the dist folder to use by default
 - `css_url` / `js_url` — full URL override for CSS/JS

 ```php
 <?php
 use BootstrapPHP\Bootstrap;

 $cfg = Bootstrap::config([
   // package: 'css' or 'js'
   'package' => 'js',
   // type: grid | reboot | utilities | bundle | esm | None
   'type' => 'bundle',
   // build: min | None
   'build' => 'min',
   // rtl: true | false (CSS only)
   'rtl' => false
 ]);

 echo Bootstrap::assets($cfg);
 ```

Example: CSS RTL

```php
<?php
use BootstrapPHP\Bootstrap;

$cfgcss = Bootstrap::config([
    'package' => 'css',
    'type' => 'None',
    'build' => 'min',
    'rtl' => true,
]);

echo Bootstrap::assets($cfgcss);
// -> /vendor/twbs/bootstrap/dist/css/bootstrap.rtl.min.css
```

 **CSS / JS variants**

 The helpers support common CSS/JS variants (bundle/esm, grid/reboot/utilities, RTL, minified). Use `cssUrl()` / `jsUrl()` on the config to request variants.

 **Asset publishing**

 This package does not copy vendor assets to a web root. In many deployments you should publish or symlink `vendor/twbs/bootstrap/dist` into your public assets folder (for example `public/vendor/bootstrap`) or set `asset_base_url` to a web-accessible path.

 Example (simple copy):

 ```bash
 mkdir -p public/vendor/bootstrap
 cp -r vendor/twbs/bootstrap/dist/* public/vendor/bootstrap/
 ```

 Or set `asset_base_url` to the published path.

 **API highlights**

 - `Bootstrap::assets($config = null)` — prints `<link>`/`<script>` tags for CSS and JS
 - `Bootstrap::cssUrl($variant, $rtl = false, $minified = true)` — returns CSS path for a variant
 - `Bootstrap::jsUrl($variant = 'bundle', $minified = true)` — returns JS path for a variant
 - `Bootstrap::component($tag, $content, $options = [])` — generic renderer
 - Entrypoint classes: `Bootstrap::components()`, `Bootstrap::content()`, `Bootstrap::forms()`, `Bootstrap::helpers()`, `Bootstrap::layout()`, `Bootstrap::utilities()`

 **Development notes**

 - When this package is installed as a Composer dependency, the runtime detection attempts to locate the Bootstrap dist directory relative to the package location. If detection fails, explicitly set `asset_base_url`.
 - Run `php -l` to verify syntax across files during development.

 **Contributing**

 Contributions welcome — open issues or PRs. Keep changes small and unit-test where appropriate.

 **License**

 This project follows the LICENSE file in the repository.
