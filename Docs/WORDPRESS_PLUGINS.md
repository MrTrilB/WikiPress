# WordPress Plugin Integration

A normal WordPress plugin can extend WikiPress without being placed in the internal plugin directory. The bridge is the `wikipress_register_plugin` action.

## Register an Extension

The WordPress plugin should load its extension class and register it when WikiPress has initialized its plugin manager:

```php
<?php
/**
 * Plugin Name: My WikiPress Extension
 */

use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;

add_action('wikipress_register_plugin', static function (Plugins $plugins): void {
    $plugins->register_plugin_instance(new MyWikiPressExtension());
});

final class MyWikiPressExtension implements PluginInterface {
    public function get_slug(): string { return 'my-wikipress-extension'; }
    public function get_name(): string { return 'My WikiPress Extension'; }
    public function get_version(): string { return '1.0.0'; }
    public function get_author(): string { return 'Example Author'; }
    public function get_author_uri(): string { return 'https://example.com'; }
    public function get_description(): string { return 'Adds a WikiPress integration.'; }
    public function get_uri(): string { return 'https://example.com'; }
    public function get_license(): string { return 'GPL-2.0-or-later'; }
    public function is_active(): bool { return true; }
    public function init(): void {}
}
```

The callback receives the `Plugins` manager. `Plugins::register_plugin()` is also available as a static convenience method, but using the supplied manager makes the integration explicit.

## Composer and Class Loading

The extension must load its class before registration. Use the WordPress plugin's Composer autoloader when available, or require the extension file directly from the plugin bootstrap. Keep the extension namespace separate from WikiPress and declare a dependency on WikiPress in the plugin documentation or activation checks.

## Optional Providers

The same optional interfaces are available to a WordPress-installed extension:

- settings and generated settings tabs
- database table registration
- asset registration
- admin page registration
- REST route registration
- frontend registration
- translation loading

Example REST provider:

```php
final class MyWikiPressExtension implements PluginInterface, RestRouteProviderInterface {
    public function register_rest_routes(): void {
        register_rest_route('my-extension/v1', '/status', [
            'methods' => 'GET',
            'callback' => [ $this, 'status' ],
            'permission_callback' => '__return_true',
        ]);
    }

    public function status(WP_REST_Request $request): WP_REST_Response {
        return Response::success([ 'ready' => true ]);
    }
}
```

Import `Response` from `TrilBDev\\WikiPress\\API` when returning WikiPress-style response envelopes.

## Lifecycle Considerations

WikiPress calls the registration action after internal plugin discovery. If auto-activation is enabled, a plugin registered during that action is initialized immediately. The manager checks `is_active()` before invoking provider methods and `init()`.

Do not assume WikiPress is available before the registration action. If the action never fires, the integration should remain inactive rather than causing a fatal error. Use `class_exists()` or an activation dependency check when your plugin can be activated independently.

## Settings Integration

Implement `SettingsProviderInterface` to register defaults and `SettingsPageProviderInterface` to expose a generated tab. Read values through `TrilBDev\\WikiPress\\Includes\\Settings\\Settings` and sanitize all submitted values before storing them. See [SETTINGS.md](SETTINGS.md).

## Recommended Integration Checklist

1. Declare WikiPress as a dependency or check for its classes.
2. Load the extension class before the registration hook runs.
3. Register on `wikipress_register_plugin`.
4. Use a unique slug and namespace.
5. Implement only the provider interfaces the extension needs.
6. Use WikiPress helpers for sanitization, permissions, URLs, and responses.
7. Test activation with WikiPress active and inactive.
