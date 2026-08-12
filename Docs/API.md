# WikiPress REST API

The REST API is registered by `TrilBDev\\WikiPress\\API\\Routes` on `rest_api_init`.

Base namespace:

```text
/wp-json/wikipress/v1
```

## Permissions

Read callbacks currently return `true`. Write callbacks require the `edit_posts` capability through `PermissionHelper::can()`.

The API also applies the `wikipress_wiki_access_allowed` filter when reading wiki content and related pages. Use that filter to add site-specific access rules.

## Endpoints

| Method | Route | Purpose |
| --- | --- | --- |
| GET | `/wikis` | List published or available wikis |
| POST | `/wikis` | Create a wiki |
| GET | `/wikis/{id}` | Retrieve one wiki |
| POST, PUT, PATCH | `/wikis/{id}` | Update one wiki |
| DELETE | `/wikis/{id}` | Delete one wiki |
| GET | `/pages` | List wiki pages |
| POST | `/pages` | Create a page |
| GET | `/pages/{id}` | Retrieve one page |
| POST, PUT, PATCH | `/pages/{id}` | Update one page |
| DELETE | `/pages/{id}` | Delete one page |

Collection endpoints accept:

- `per_page`: integer from 1 to 100, default 20
- `page`: integer from 1, default 1
- `search`: text search value

Wiki status values are `draft`, `publish`, and `private`. The delete endpoints accept `force=true` to bypass the trash.

## Payloads

Wiki payload:

```json
{
  "title": "Developer Guide",
  "description": "Reference material for the wiki.",
  "status": "publish"
}
```

Page payload:

```json
{
  "title": "Getting Started",
  "content": "Page content.",
  "excerpt": "A short summary.",
  "status": "publish",
  "wiki_id": 42,
  "categories": ["Guides"],
  "tags": ["intro", "setup"]
}
```

Wiki payloads may also include `permalink`, a tokenized pattern override for the Wiki's pages. Patterns use the tokens documented in [HELPERS.md](HELPERS.md). The normalized override is returned as `permalink` and is stored per Wiki; omit or submit an empty value to use the global setting.

## Response Shape

Successful responses use `Response::success()`:

```json
{
  "success": true,
  "data": {
    "id": 42,
    "name": "Developer Guide"
  }
}
```

Create operations return HTTP 201. Read and update operations normally return HTTP 200. Errors use `WP_Error` with a status code, for example:

```json
{
  "code": "not_found",
  "message": "Wiki not found.",
  "data": { "status": 404 }
}
```

## Programmatic API

Use `TrilBDev\\WikiPress\\API\\API` when code needs the same operations without making an HTTP request:

```php
use TrilBDev\WikiPress\API\API;

$wikis = API::list_wikis([
    'posts_per_page' => 10,
    'paged' => 1,
    's' => 'developer',
]);

$wiki = API::get_wiki(42);
```

Available methods include `list_wikis()`, `get_wiki()`, `create_wiki()`, `update_wiki()`, `delete_wiki()`, and the corresponding page methods.

`format_wiki()` and `format_post()` are public formatting methods used when an extension needs the same response shape as the REST API.

## Extension Hooks

- `wikipress_wiki_payload` filters the sanitized Wiki payload before creation or update. It receives the payload and the existing `WP_Post` when updating, or `null` when creating.
- `wikipress_wiki_saved` fires after a Wiki is created or updated and receives the Wiki ID and final payload.
- `wikipress_wiki_access_allowed` filters whether a Wiki is readable. The filter is also applied when returning related Wiki pages.

Collection totals are taken from the underlying WordPress query before access filtering; the returned `items` array may therefore contain fewer entries than `total` when access rules are active.

## Adding Routes

An internal or external extension should implement `RestRouteProviderInterface` and register routes from its `register_rest_routes()` method. Keep route callbacks thin: validate request values, call a domain service, and return `Response::success()` or a `WP_Error`.

Use `Schema::wiki()`, `Schema::page()`, and `Schema::collection_parameters()` as references for request definitions. `Validators::wiki_payload()` and `Validators::page_payload()` provide reusable validation results with `valid`, `errors`, and sanitized `payload` keys.
