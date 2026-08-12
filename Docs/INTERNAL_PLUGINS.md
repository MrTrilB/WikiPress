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
- `ShortcodeProviderInterface::get_shortcodes()`
- `AssetsProviderInterface::register_assets()`
- `AdminPageProviderInterface::register_admin_pages()`
- `RestRouteProviderInterface::register_rest_routes()`
- `FrontendProviderInterface::register_frontend()`
- `I18nProviderInterface::load_textdomain()`

The loader invokes provider methods in this order for an active plugin: settings, database tables, shortcodes, assets, admin pages, REST routes, frontend behavior, translations, then `init()`.

Shortcode providers should return definitions created with `ShortcodeHelper::define()` from `get_shortcodes()`. The loader registers them with the shared shortcode registry.

## Composition and Includes

WikiPress core service classes are final. Compose them from the plugin rather than extending them. Use `Includes/Includes.php` to load feature classes and `Includes/I18n.php` for the text domain loader.

Use `LoaderHelper` to register hooks from component classes:

```php
$this->loader->register_component($this, [
    [ 'type' => 'action', 'hook' => 'init', 'callback' => 'register_feature' ],
])->run();
```

`Includes/Shortcodes.php` is loaded automatically when present, alongside `Includes/Includes.php` and `Includes/I18n.php`.

## Assets

Register page-specific assets through the shared `Assets` service or the plugin's own `Assets` component. Keep source JavaScript and Sass under `Assets/js` and `Assets/scss`; compiled output belongs under `Assets/dist/js` and `Assets/dist/css`.

Build the repository assets from the WikiPress root:

```bash
npm run build
```

## Discovery and Activation

WikiPress discovers top-level PHP files and structurally valid plugin subdirectories during `Plugins::init()`. Auto-activation is controlled by the `wiki_plugin_auto_activate` setting. A plugin can also return `false` from `is_active()` to remain registered but uninitialized.

For debugging, use `LoggerHelper`; the loader logs invalid plugin classes and thrown initialization errors.

## Discovery and Registry APIs

`Plugins::get_instance()` returns the manager singleton. `get_loaded_plugins()` returns discovered class names and `get_registered_plugins()` returns registered instances. Use `register_plugin_instance()` inside the `wikipress_register_plugin` callback, or `Plugins::register_plugin()` as the static convenience method. Duplicate slugs are ignored.

`is_plugin_enabled()` reads the persisted enabled state, defaulting to enabled when no state exists. `set_plugin_enabled()` persists a registered plugin's state. A plugin initializes only when auto-activation is enabled, its persisted state is enabled, and `is_active()` returns true.

## Shared Assets

Use `Assets::register_page( $page, $assets )` for page-specific styles and scripts. Asset descriptors support `handle`, `src`, `deps`, `version`, `in_footer`, and `media` where applicable. The shared asset service exposes `wikipress_base_assets`, `wikipress_admin_assets`, and `wikipress_frontend_assets` filters for generic extension of the asset bundle.

## Localization

Run `npm run i18n:pot` to scan WikiPress core and every internal plugin, writing the core catalog under `src/languages` and plugin catalogs under each plugin's `Language` directory. Run `npm run i18n:mo` to compile `.mo` files, using a matching `.po` file when present and otherwise the `.pot` template. Keep plugin strings in the owning plugin's text domain and language directory.
