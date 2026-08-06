# WikiPress Settings

WikiPress settings are stored in the WikiPress settings database table through `SettingsManager`. Groups are stored with a `wikipress_` storage prefix while callers use the logical group name.

## Reading and Writing Values

Use the `Settings` facade rather than querying the database directly:

```php
use TrilBDev\WikiPress\Includes\Settings\Settings;

$name = Settings::get_string('root_name', 'WikiPress');
$slug = Settings::get_slug('root_slug', 'wiki');
$show_toc = Settings::get_bool('show_toc', true);

Settings::set('show_toc', false);
Settings::delete('show_toc');
```

Available typed readers are `get()`, `get_string()`, `get_key()`, `get_slug()`, `get_int()`, and `get_bool()`. Group methods are `get_group()`, `set_group()`, and `get_all()`. `has()` checks whether a stored key exists.

## Core Groups

Core defaults currently include:

- `general`: `root_name`, `root_slug`, `category_slug`, `tag_slug`, `permalink`
- `layout`: `show_search`, `show_toc`
- `access`: extension-defined access keys
- `tools`: `debug_logging`

Use `Settings::register_group()` or `Settings::register_key()` for extension settings. Registered defaults are used during installation and fallback reads.

## Registering an Extension Group

```php
use TrilBDev\WikiPress\Includes\Settings\Settings;

final class SettingsProvider {
    public function register(): void {
        Settings::register_group('my_extension', [
            'enabled' => true,
            'mode' => 'safe',
        ]);
    }
}
```

Register groups before settings installation or before values are read. The plugin system calls `SettingsProviderInterface::register_settings()` before `init()` for active extensions.

## Settings Page Provider

An extension can supply a generated settings tab by implementing `SettingsPageProviderInterface`:

```php
public function get_settings_page(): array {
    return [
        'slug' => 'my-extension',
        'label' => __( 'My Extension', 'my-extension' ),
        'title' => __( 'My Extension settings', 'my-extension' ),
        'fields' => [
            [
                'key' => 'my_extension_enabled',
                'label' => __( 'Enabled', 'my-extension' ),
                'type' => 'checkbox',
                'default' => true,
            ],
        ],
    ];
}

public function sanitize_settings($input): array {
    $input = is_array($input) ? $input : [];
    $enabled = ! empty($input['my_extension_enabled']);

    Settings::set_group('my_extension', [
        'my_extension_enabled' => $enabled,
    ]);

    return [ 'my_extension_enabled' => $enabled ];
}
```

The actual field types supported by the admin settings renderer should be checked against the current settings page implementation before adding new types. Every submitted value must be normalized and validated before persistence.

## Migration and Persistence

`SettingsManager::install()` installs the settings table, merges registered defaults with stored values, migrates legacy unprefixed groups, and removes the legacy rows. `set_group()` uses a database replace operation and serializes the group.

Do not store secrets in settings unless the feature has an explicit encryption strategy. Sensitive credentials should use the project encryption conventions rather than plain serialized values.

## Settings Checklist

1. Register the group and defaults.
2. Register the provider through the WikiPress plugin lifecycle.
3. Read values through typed `Settings` methods.
4. Sanitize submitted values with `SanitizationHelper` or a provider-specific sanitizer.
5. Persist the complete logical group with `set_group()` when appropriate.
6. Escape values when rendering HTML.
