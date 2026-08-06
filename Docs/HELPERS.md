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

## FormFieldHelper

`FormFieldHelper` renders Bootstrap-compatible controls and handles attributes, validation feedback, and escaping. Common methods include `input()`, `text_input()`, `textarea()`, `select()`, `checkbox()`, `radio()`, and `switch()`.

```php
echo FormFieldHelper::text_input(
    'wiki_title',
    $title,
    [ 'class' => 'form-control-lg', 'required' => true ]
);
```

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

- `AjaxHelper` centralizes AJAX nonce and response handling.
- `AlertHelper` renders standard admin alerts.
- `LoggerHelper` writes diagnostic messages.

Prefer these helpers over duplicating sanitization, URL construction, capability checks, query setup, or form markup in feature classes.
