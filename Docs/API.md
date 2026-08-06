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

## Adding Routes

An internal or external extension should implement `RestRouteProviderInterface` and register routes from its `register_rest_routes()` method. Keep route callbacks thin: validate request values, call a domain service, and return `Response::success()` or a `WP_Error`.

Use `Schema::wiki()`, `Schema::page()`, and `Schema::collection_parameters()` as references for request definitions. `Validators::wiki_payload()` and `Validators::page_payload()` provide reusable validation results with `valid`, `errors`, and sanitized `payload` keys.
