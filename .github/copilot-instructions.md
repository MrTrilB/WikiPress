# WikiPress Architecture Instructions

WikiPress is a modular WordPress platform. Internal plugins and separately installed WordPress plugins are the primary way to add features and integrations. Preserve this architecture in every change.

## Core Boundary

- Keep feature-specific behavior out of WikiPress core. Core must not contain conditions, selectors, settings keys, labels, capabilities, or business rules for a particular plugin.
- Do not add plugin-specific branches to shared classes such as admin renderers, settings managers, asset managers, API services, or shared helpers.
- If a plugin needs behavior that core cannot currently express, first implement it inside the plugin using its own `Includes/`, `Assets/`, helpers, services, and hooks.
- Decide whether to change core using this decision gate:
  1. Can the behavior live entirely in the plugin? If yes, do that.
  2. If no, does an identical need exist in two or more existing plugins? If no, stop — do not change core.
  3. If yes, add a generic extension point; never reference a plugin slug or class in core.
- When adapting core, expose a stable extension point and let the plugin provide the data or behavior. Never make core identify the plugin by slug or class name.
- Prefer composition over inheritance. Core service classes are generally final and should be composed by plugins.

## Plugin Ownership

- Internal plugins live under `src/includes/Plugins/<PluginName>/`; separately installed integrations register through `wikipress_register_plugin`.
- Separately installed plugins follow the same boundary rules as internal plugins. They must not require core changes that reference their slug or class; instead, use the same provider interfaces and generic extension points documented in WORDPRESS_PLUGINS.md.
- A plugin owns its settings defaults, settings sanitization, business rules, templates, JavaScript, Sass, translations, REST routes, admin pages, and feature-specific helpers.
- Implement only the optional provider interfaces required by the feature: settings, settings page, database, assets, admin pages, REST routes, frontend behavior, translations, and similar contracts.
- Use the plugin lifecycle and `LoaderHelper` for hooks. Keep bootstrap classes thin and delegate feature logic to plugin-owned components.
- Use unique plugin slugs and namespaces. Do not place plugin settings in core settings groups unless the setting is genuinely a core concern.
- Plugin settings pages should provide declarative field metadata to the generic renderer. Put dynamic option generation, validation, conditional behavior, and persistence in the plugin.
- Plugin JavaScript must discover and control plugin-owned markup through plugin data attributes or generic metadata. Do not require core to add a marker for one plugin.

## Reusable Core APIs

- Before adding local logic, check the existing APIs and helpers in `src/API`, `src/includes/Functions/Helpers`, `src/includes/Settings`, and the provider interfaces in `src/includes/Plugins/PluginsInterface.php`.
- Prefer `Settings` and `SettingsManager` for settings access, `SanitizationHelper` and `RequestHelper` for input normalization, `PermissionHelper` for authorization, `FormFieldHelper` for controls, `AjaxHelper` for AJAX responses, `AlertHelper` for admin notices, and `Response`/`Validators` for REST responses and validation.
- Do not query the settings database directly, duplicate shared sanitization or permission logic, or hand-build shared form/alert markup when a project helper exists.
- Use hooks, filters, provider interfaces, and generic metadata to deepen integration without coupling extensions to core internals.

## Localization

- WikiPress and its plugins use the repository language scripts to manage translation catalogs and compiled language files.
- Run `npm run i18n:pot` after adding or changing translatable strings. The script scans core and plugin source files and writes the appropriate POT catalog under `src/languages` or the plugin's `Language/` directory.
- Run `npm run i18n:mo` after updating a POT or PO catalog to compile the corresponding MO files used by WordPress.
- Keep plugin translation catalogs and language files inside the owning plugin's `Language/` directory. Do not move plugin strings into the core catalog or hard-code translated text in shared core code.
- Use the correct text domain for the owning component and preserve existing translation file naming conventions.

## Change Workflow

1. Read the relevant README and documentation before changing architecture. Start with [README.md](../README.md), then consult [API.md](../Docs/API.md), [HELPERS.md](../Docs/HELPERS.md), [SETTINGS.md](../Docs/SETTINGS.md), [INTERNAL_PLUGINS.md](../Docs/INTERNAL_PLUGINS.md), and [WORDPRESS_PLUGINS.md](../Docs/WORDPRESS_PLUGINS.md) as applicable.
2. Identify the owning plugin or extension boundary before editing. State whether the requested behavior belongs in a plugin or requires a generic core capability.
3. Prefer the smallest plugin-owned implementation. If core must change, document the generic contract and keep the implementation independent of any plugin identity.
4. Preserve existing public APIs unless a backward-compatible extension is required. Update documentation when a core contract or extension pattern changes.
5. Validate changed PHP with `php -l`, rebuild frontend assets with `npm run build` when source assets change, regenerate language catalogs with `npm run i18n:pot` and `npm run i18n:mo` when translatable strings change, and run `git diff --check`.

## Documentation References

- [REST API](../Docs/API.md)
- [Helpers](../Docs/HELPERS.md)
- [Settings](../Docs/SETTINGS.md)
- [Internal plugins](../Docs/INTERNAL_PLUGINS.md)
- [WordPress plugin integration](../Docs/WORDPRESS_PLUGINS.md)
- [Plugin interfaces](../src/includes/Plugins/PluginsInterface.php)
