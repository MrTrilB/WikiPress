# Internal WikiPress Plugins

Internal plugins are modular extensions stored in the configured WikiPress plugin directory. The default directory is `WIKIPRESS_PLUGINS`; the `wikipress_plugin_directory` setting can override it with an absolute path or a path relative to the WikiPress root.

## Required Structure

The loader discovers a plugin only when its directory has the required structure:

```text
MyPlugin/
|- Assets/
|  |- dist/css/
|  |- dist/js/
|  |- Assets.php
|  |- js/
|  `- scss/
|- Includes/
|  |- Includes.php
|  |- I18n.php
|  `- Settings/Settings.php
|- Language/
`- MyPlugin.php
```

The main file must be named after the directory, declare a namespace, define a class with the same filename, and implement `PluginInterface`.

## Required Contract

```php
namespace TrilBDev\WikiPress\Includes\Plugins\MyPlugin;

use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;

final class MyPlugin implements PluginInterface {
    public function get_slug(): string { return 'my-plugin'; }
    public function get_name(): string { return 'My Plugin'; }
    public function get_version(): string { return '1.0.0'; }
    public function get_author(): string { return 'Example Author'; }
    public function get_author_uri(): string { return 'https://example.com'; }
    public function get_description(): string { return 'An example WikiPress extension.'; }
    public function get_uri(): string { return 'https://example.com/my-plugin'; }
    public function get_license(): string { return 'GPL-2.0-or-later'; }
    public function is_active(): bool { return true; }

    public function init(): void {
        // Feature initialization belongs here.
    }
}
```

The slug must be unique. The loader ignores duplicate slugs and logs initialization failures.

## Optional Capabilities

Implement these interfaces only when the extension needs the capability:

- `SettingsProviderInterface::register_settings()`
- `SettingsPageProviderInterface::get_settings_page()` and `sanitize_settings()`
- `DatabaseProviderInterface::register_tables()`
- `AssetsProviderInterface::register_assets()`
- `AdminPageProviderInterface::register_admin_pages()`
- `RestRouteProviderInterface::register_rest_routes()`
- `FrontendProviderInterface::register_frontend()`
- `I18nProviderInterface::load_textdomain()`

The loader invokes provider methods in this order for an active plugin: settings, database tables, assets, admin pages, REST routes, frontend behavior, translations, then `init()`.

## Composition and Includes

WikiPress core service classes are final. Compose them from the plugin rather than extending them. Use `Includes/Includes.php` to load feature classes and `Includes/I18n.php` for the text domain loader.

Use `LoaderHelper` to register hooks from component classes:

```php
$this->loader->register_component($this, [
    [ 'type' => 'action', 'hook' => 'init', 'callback' => 'register_feature' ],
])->run();
```

## Assets

Register page-specific assets through the shared `Assets` service or the plugin's own `Assets` component. Keep source JavaScript and Sass under `Assets/js` and `Assets/scss`; compiled output belongs under `Assets/dist/js` and `Assets/dist/css`.

Build the repository assets from the WikiPress root:

```bash
npm run build
```

## Discovery and Activation

WikiPress discovers top-level PHP files and structurally valid plugin subdirectories during `Plugins::init()`. Auto-activation is controlled by the `wiki_plugin_auto_activate` setting. A plugin can also return `false` from `is_active()` to remain registered but uninitialized.

For debugging, use `LoggerHelper`; the loader logs invalid plugin classes and thrown initialization errors.
