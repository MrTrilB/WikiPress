# WikiPress Helpers

Reusable helpers live in `src/includes/Functions/Helpers` and use the namespace `TrilBDev\\WikiPress\\Includes\\Functions\\Helpers`.

## SanitizationHelper

Use defensive wrappers around WordPress sanitizers:

- `text($value, $fallback = '')`
- `textarea($value, $fallback = '')`
- `key($value, $fallback = '')`
- `slug($value, $fallback = '')`
- `integer($value, $fallback = 0)`
- `integer_range($value, $minimum, $maximum, $fallback)`
- `one_of($value, $allowed, $fallback)`
- `terms($terms)` for comma-separated strings or arrays

```php
$title = SanitizationHelper::text($payload['title'] ?? '');
$status = SanitizationHelper::one_of($payload['status'] ?? '', [ 'draft', 'publish' ], 'draft');
$tags = SanitizationHelper::terms($payload['tags'] ?? []);
```

## RequestHelper

Use `RequestHelper` for sanitized request-like values. It unslashes values before sanitizing.

- `get()`, `get_text()`, `get_key()`, `get_integer()` read from `$_GET`
- `value()`, `text()`, `key()`, `slug()`, `integer()` read from a supplied array
- `integer_range()`, `array()`, and `boolean()` handle bounded numbers, arrays, and booleans

```php
$page = RequestHelper::get_key('page', 'wikipress');
$per_page = RequestHelper::integer_range($request->get_param('per_page'), 1, 100, 20);
$force = RequestHelper::boolean([ 'force' => $request->get_param('force') ], 'force', false);
```

## PermissionHelper

Centralize capability and authentication checks:

- `can($capability, $object_id = 0)`
- `can_any($capabilities, $object_id = 0)`
- `can_all($capabilities, $object_id = 0)`
- `logged_in()`
- `user_id()`

```php
if ( ! PermissionHelper::can('edit_posts') ) {
    return new WP_Error('forbidden', 'You cannot edit this content.', [ 'status' => 403 ]);
}
```

## PostHelper and QueryHelper

`QueryHelper::current()` returns the current `WP_Query`; `QueryHelper::posts($args)` creates a reusable `WP_Query`.

`PostHelper` provides null-safe post operations:

- `current()`, `current_id()`, `current_type()`
- `get($post)`, `id($post)`
- `is_type()`, `is_wiki()`, `is_wiki_page()`
- `permalink($post)`

## TaxonomyHelper

- `terms($taxonomy, $post_id = 0, $limit = 0, $search = '')`
- `ids($terms)`
- `names($terms)`

The helper normalizes term objects, IDs, names, comma-separated strings, and invalid results.

## ContentHelper

- `plain_text($content)` strips shortcodes and HTML
- `word_count($content)` returns a safe word count
- `reading_time($content, $words_per_minute = 200)` returns minutes, minimum 1
- `excerpt($content, $words = 30)` creates a plain-text excerpt
- `heading_id($heading, $fallback = 'section')` creates a slug ID

## UrlHelper

Build URLs consistently:

- `admin_page($page, $args = [])`
- `admin_action($action, $args = [])`
- `nonce($url, $action)`
- `admin_action_nonce($action, $nonce_action, $args = [])`

Always escape the returned URL at output time with `esc_url()`.

## PermalinkHelper

`PermalinkHelper` builds tokenized Wiki page URLs and resolves them through WordPress rewrite rules. Supported tokens are `%root%`, `%root_category%`, `%root_tags%`, `%wiki%`, `%wiki_category%`, `%wiki_tag%`, and `%wiki_page%`.

- `token_definitions()` returns the translated token descriptions.
- `default_pattern()` returns the default token pattern.
- `sanitize_pattern($pattern)` keeps supported tokens and sanitized literal segments.
- `pattern_for_wiki($wiki_id = 0)` resolves the per-Wiki override or global `permalink` setting.
- `page_url($page)` returns the full URL for a `WP_Post` Wiki page.
- `expand($pattern, $page, $wiki = null)` expands a pattern into a relative path.
- `rewrite_rule()` registers the `wikipress_path` query variable and request resolver.
- `resolve_request($vars)` maps a tokenized path to a published Wiki page.
- `filter_page_permalink($link, $post)` replaces the normal link for Wiki page posts.

Wiki permalink overrides are stored in `_wikipress_permalink`. Use `sanitize_pattern()` before persisting custom patterns.

## AjaxHelper

Use `AjaxHelper` for common AJAX authorization and JSON responses:

- `is_ajax_request()`, `request_method()`, and `is_method($method)` inspect the request.
- `has_valid_nonce($action, $field = 'nonce', $request = null)` verifies a WordPress nonce.
- `can($capability, $object_id = 0)` delegates capability checks to `PermissionHelper`.
- `authorized($action, $capability = '', $object_id = 0, $field = 'nonce')` combines nonce and capability checks.
- `success($data = null, $status_code = 200)` and `error($data = null, $status_code = 400)` send JSON responses.
- `unauthorized($message, $status_code = 403)` sends a standard authorization error.

## AlertHelper

Use `AlertHelper` for shared Bootstrap admin notices. `admin_error()`, `admin_success()`, `admin_warning()`, and `admin_info()` render notices immediately. `render_admin_notice()` accepts `info`, `success`, `warning`, or `error`; `get_admin_notice()` returns the escaped HTML string for AJAX responses or deferred insertion.

## LoggerHelper

`LoggerHelper::write_log($value)` writes to the PHP debug log, and `write_console($value)` emits a browser console message. Logging is enabled when `WP_DEBUG` is enabled or the WikiPress `debug_logging` setting is true; console output also respects `console_logging`.

## FormFieldHelper

`FormFieldHelper` renders Bootstrap-compatible controls and handles attributes, validation feedback, and escaping. Common methods include `input()`, `text_input()`, `textarea()`, `select()`, `checkbox()`, `radio()`, `switch()`, `label()`, `button()`, `button_group()`, and dropdown button helpers.

```php
echo FormFieldHelper::text_input(
    'wiki_title',
    $title,
    [ 'class' => 'form-control-lg', 'required' => true ]
);
```

`bootstrap_select()` and `bootstrap_multiselect()` render controls using the shared Bootstrap Select integration. They accept `data` options, `selected` values, `attributes`, and presentation options such as `live_search`, `dropup_auto`, `show_tick`, `selection_indicator`, `placeholder`, `width`, and `actions_box`. `bootstrap_multiselect()` automatically adds `[]` to the field name and enables `multiple`.

`attributes_to_string()` converts an attribute array into escaped HTML attributes and is useful when composing extension-owned markup. Prefer the helper controls over hand-built form HTML.

## ShortcodeHelper

Define and register extension shortcodes through the shared registry:

```php
use TrilBDev\WikiPress\Includes\Functions\Helpers\ShortcodeHelper;

$definition = ShortcodeHelper::define(
    'my_status',
    static fn( array $attributes, ?string $content, string $tag ): string => esc_html( $attributes['label'] ),
    [ 'label' => 'Ready' ],
    [ 'description' => 'Displays a status label.', 'category' => 'my-plugin' ]
);

ShortcodeHelper::register( $definition );
```

Use `register_many($definitions, $replace = false)` for a group. Definitions support `tag`, `callback`, `attributes`, `description`, `category`, `enclosing`, and `tinymce`. Shortcode callbacks must return their output.

## LoaderHelper

`LoaderHelper::register_component()` registers multiple actions or filters for one component. Each definition contains `type`, `hook`, and `callback`, with optional `priority` and `accepted_args`.

```php
$this->loader->register_component($this, [
    [ 'type' => 'action', 'hook' => 'init', 'callback' => 'register_content' ],
    [ 'type' => 'filter', 'hook' => 'the_content', 'callback' => 'filter_content' ],
])->run();
```

Invalid hook definitions throw `InvalidArgumentException`; use `action` or `filter` only.

## Other Helpers

- `ShortcodeHelper` and `Core\Shortcodes` provide a shared shortcode registry.
- `Response` and `Validators` provide consistent REST envelopes and payload validation.
- `Settings`, `SettingsManager`, and `PermissionHelper` cover shared persistence and authorization contracts.

Prefer these helpers over duplicating sanitization, URL construction, capability checks, query setup, or form markup in feature classes.
