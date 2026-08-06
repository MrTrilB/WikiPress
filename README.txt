=== WikiPress ===
Contributors: trilbdev
Tags: wiki, knowledge base, documentation, modular, rest api
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modular wiki platform for WordPress by TrilB.Dev, featuring custom wiki post types, grouped settings, REST API endpoints, shared helpers, Bootstrap-powered admin UI, and an extension system for internal and external plugins.

== Description ==
WikiPress is a modular wiki framework for WordPress, built by TrilB.Dev to provide a structured, scalable foundation for knowledge bases, documentation hubs, and collaborative content systems. It introduces dedicated wiki and wiki‑page post types, complete with taxonomies and a streamlined admin experience designed for high‑volume content management.

### Under the Hood
WikiPress includes a wide array of built‑in modules designed to support every aspect of creating and maintaining a powerful wiki inside WordPress. These internal plugins handle core features such as content types, taxonomy registration, REST routing, settings storage, admin UI components, and shared helper libraries. Each module is purpose‑built, self‑contained, and designed to work together seamlessly, giving you a stable foundation for building documentation systems, knowledge bases, and content‑heavy wiki platforms.

### Built for Expansion
WikiPress is engineered for extensibility at its core. It automatically detects WordPress plugins that are built to expand WikiPress, allowing developers to register new features, admin tools, content structures, or REST endpoints with minimal boilerplate. With a wide array of helpers, utilities, and APIs—covering sanitization, permissions, queries, content formatting, URLs, forms, and more—developers can extend WikiPress cleanly and consistently. Whether you’re building internal modules or standalone WordPress plugins, the system gives you everything you need to integrate with ease.

### Developer Architecture
Developers gain access to a clean, consistent architecture that encourages modular design and predictable behavior. WikiPress provides database‑backed settings grouped by feature, shared helper libraries, Bootstrap‑based admin assets compiled via Webpack and Sass, and a fully documented REST API under /wp-json/wikipress/v1. Every part of the system is built to reduce friction, improve reliability, and make custom development faster—whether you’re enhancing WikiPress itself or embedding wiki functionality into a larger WordPress ecosystem.

Whether you're building a documentation platform, a knowledge wiki, or a custom content‑driven application, WikiPress provides the structure, tools, and extensibility you need.

== Features ==
Wiki and wiki‑page post types

Custom taxonomies for structured content

REST API endpoints under /wp-json/wikipress/v1

Database‑backed settings grouped by feature

Shared sanitization, request, permission, query, content, URL, and form helpers

Bootstrap‑based admin UI compiled with Webpack and Sass

Font Awesome integration

Internal WikiPress plugin discovery

Widgets and Block for Elementor & Gutenberg Editors

Integration with separately installed WordPress plugins

== Installation ==
Upload the plugin files to /wp-content/plugins/wikipress/, or install via the WordPress plugin installer.

Activate the plugin through the Plugins menu.

Access WikiPress settings under WikiPress → Settings.

Begin creating wiki content using the Wiki and Wiki Page post types.

== Frequently Asked Questions ==
Does WikiPress work with custom themes?
Yes. WikiPress is theme‑agnostic and works with any properly coded WordPress theme.

Can I extend WikiPress with my own plugin?
Absolutely. WikiPress automatically detects compatible extension plugins and provides helper libraries and APIs to make development easy.

Is there a REST API?
Yes. All core wiki functionality is exposed under /wp-json/wikipress/v1.

Does WikiPress include its own styling?
The admin UI uses Bootstrap and Font Awesome. Frontend styling is intentionally minimal so themes can control presentation.

== Screenshots ==
WikiPress admin dashboard

Wiki post type editor

Wiki‑page hierarchy view

Settings grouped by feature

== Changelog ==
1.0.0
Initial release

Wiki and wiki‑page post types

REST API endpoints

Modular internal plugin system

Shared helper libraries

Bootstrap + Sass admin UI

Extension detection system

== Upgrade Notice ==
1.0.0
Initial release of WikiPress. No upgrade actions required.