# WikiPress

WikiPress is a modular wiki platform for WordPress by TrilB.Dev, featuring custom wiki post types, grouped settings, REST API endpoints, shared helpers, Bootstrap‑powered admin UI, Font Awesome support, and an extension system for internal and external plugins.

## Requirements

- WordPress with REST API support
- PHP version supported by the current WikiPress codebase and its Composer dependencies
- Node.js and npm only when rebuilding frontend assets

Install PHP dependencies with Composer and build the frontend assets with:

```bash
npm install
npm run build
```

The build writes compiled assets to `src/Assets/dist`.

## Core Features

- Wiki and wiki-page content management
- REST endpoints under `/wp-json/wikipress/v1`
- Database-backed settings grouped by feature
- Shared sanitization, request, permission, query, content, URL, and form helpers
- Bootstrap-based admin assets compiled with Webpack and Sass
- Font Awesome integration
- Internal WikiPress plugin discovery
- Integration with separately installed WordPress plugins

## Documentation

- [REST API](Docs/API.md)
- [Helpers](Docs/HELPERS.md)
- [Settings](Docs/SETTINGS.md)
- [Internal WikiPress plugins](Docs/INTERNAL_PLUGINS.md)
- [WordPress plugin integration](Docs/WORDPRESS_PLUGINS.md)

## Project Structure

```text
src/
|- API/                         REST API services, routes, schemas, responses
|- Admin/                       Admin pages and UI managers
|- Assets/                      Shared source and compiled assets
|- includes/Functions/Helpers/ Reusable helper classes
|- includes/Plugins/            Internal plugin system and built-in extensions
|- includes/Settings/           Settings facade and database manager
`- WikiPress.php                Main bootstrap class
```

## Extension Model

A WikiPress extension implements `PluginInterface`, returns a unique slug, and exposes metadata and an `init()` method. Optional capability interfaces add settings, assets, admin pages, REST routes, frontend behavior, database tables, or translations.

Internal extensions are discovered from the configured WikiPress plugin directory. A normal WordPress plugin can register an extension by hooking `wikipress_register_plugin` and calling `Plugins::register_plugin()`.

See [INTERNAL_PLUGINS.md](Docs/INTERNAL_PLUGINS.md) and [WORDPRESS_PLUGINS.md](Docs/WORDPRESS_PLUGINS.md) for complete examples.

## Development Checks

```bash
npm run build
php -l path/to/changed-file.php
git diff --check
```

The repository currently has no configured automated test command; `npm test` is a placeholder that exits with an error.

## License

WikiPress is distributed under the license declared by the project and its individual dependencies. Confirm the applicable license before redistributing a packaged build.
