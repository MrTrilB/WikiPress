<?php
/**
 * WikiPress menu registration and sidebar definitions.
 *
 * @package WikiPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace WikiPress\Includes\Functions\Admin;

use WikiPress\Admin\Admin;
use WikiPress\Includes\Functions\Helpers\LoggerHelper;
use WikiPress\Includes\Functions\Helpers\WAMHelper;
use WikiPress\Includes\Functions\Helpers\WASMHelper;
use WikiPress\Includes\Plugins\AdminMenuProviderInterface;
use WikiPress\Includes\Plugins\AdminSidebarProviderInterface;
use WikiPress\Includes\Plugins\Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns WikiPress menu data and registration.
 *
 * Rendering remains in Admin and Sidebar. This class builds menu data,
 * applies extension filters, and calls the WordPress admin API.
 */
final class FunctionsSidebar {
	/**
	 * Register the core WordPress menu followed by plugin-provided menus.
	 *
	 * @param Admin $admin Core admin callbacks and capability resolver.
	 * @return void
	 */
	public static function register_admin_menu( Admin $admin ): void {
		foreach ( self::core_wordpress_menus( $admin ) as $menu ) {
			self::register_wordpress_menu( $menu );
		}

		foreach ( WAMHelper::filter( self::plugin_wordpress_menus() ) as $menu ) {
			self::register_wordpress_menu( $menu );
		}
	}

	/**
	 * Return the built-in and filtered WikiPress sidebar groups.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_sidebar_groups(): array {
		$groups = self::core_sidebar_groups();
		$menus  = WASMHelper::filter( self::plugin_sidebar_menus() );

		// Create parents first so children can target a parent in any order.
		foreach ( $menus as $menu ) {
			if ( '' === self::parent_slug( $menu ) ) {
				self::add_sidebar_group( $groups, $menu );
			}
		}

		foreach ( $menus as $menu ) {
			$parent = self::parent_slug( $menu );
			if ( '' !== $parent ) {
				self::add_sidebar_item( $groups, $parent, $menu );
			}
		}

		return $groups;
	}

	/**
	 * Get a WikiPress sidebar page URL.
	 *
	 * @param string $slug Page slug, optionally followed by a query string.
	 * @return string
	 */
	public static function get_admin_sidebar_menu_page_url( string $slug ): string {
		return admin_url( 'admin.php?page=' . $slug );
	}

	/** @return array<int, array<string, mixed>> */
	private static function core_wordpress_menus( Admin $admin ): array {
		return [
			[
				'name'       => __( 'WikiPress', 'wikipress' ),
				'slug'       => 'wikipress',
				'icon'       => 'dashicons-book-alt',
				'parent'     => '',
				'callback'   => [ $admin, 'render_dashboard' ],
				'capability' => $admin->capability( 'manager_wiki', 'manage_options' ),
				'position'   => 30,
			],
			[
				'name'       => __( 'Dashboard', 'wikipress' ),
				'slug'       => 'wikipress',
				'parent'     => 'wikipress',
				'callback'   => [ $admin, 'render_dashboard' ],
				'capability' => 'manage_options',
			],
			[
				'name'       => __( 'Manage Wiki', 'wikipress' ),
				'slug'       => 'wikipress-manage',
				'parent'     => 'wikipress',
				'callback'   => [ $admin, 'render_wikis' ],
				'capability' => $admin->capability( 'manager_wiki', 'manage_options' ),
			],
			[
				'name'       => __( 'Settings', 'wikipress' ),
				'slug'       => 'wikipress-settings',
				'parent'     => 'wikipress',
				'callback'   => [ $admin, 'render_settings' ],
				'capability' => 'manage_options',
			],
			[
				'name'       => __( 'Tools', 'wikipress' ),
				'slug'       => 'wikipress-tools',
				'parent'     => 'wikipress',
				'callback'   => [ $admin, 'render_tools' ],
				'capability' => $admin->capability( 'view_tools', 'manage_options' ),
			],
		];
	}

	/** @return array<string, array<string, mixed>> */
	private static function core_sidebar_groups(): array {
		return [
			'manage-wiki' => [
				'label' => __( 'Manage Wiki', 'wikipress' ),
				'icon'  => 'fa-solid fa-file-lines',
				'items' => [
					'wikipress-manage'                  => [ 'label' => __( 'Manage Wiki', 'wikipress' ), 'icon' => 'fa-solid fa-book-open-lines' ],
					'wikipress-manage&wiki=categories' => [ 'label' => __( 'Categories', 'wikipress' ), 'icon' => 'fa-book-open-lines-category' ],
					'wikipress-manage&wiki=tags'       => [ 'label' => __( 'Tags', 'wikipress' ), 'icon' => 'fa-kit fa-solid-book-open-lines-tag' ],
					'wikipress-manage&wiki=new'        => [ 'label' => __( 'New Wiki', 'wikipress' ), 'icon' => 'fa-kit fa-solid-book-open-lines-circle-plus' ],
				],
			],
			'settings' => [
				'label' => __( 'Settings', 'wikipress' ),
				'icon'  => 'fa-solid fa-gear',
				'items' => [
					'wikipress-settings&tab=general'     => [ 'label' => __( 'General', 'wikipress' ), 'icon' => 'fa-solid fa-sliders' ],
					'wikipress-settings&tab=layout'      => [ 'label' => __( 'Layout', 'wikipress' ), 'icon' => 'fa-solid fa-table-columns' ],
					'wikipress-settings&tab=plugins'     => [ 'label' => __( 'Plugins', 'wikipress' ), 'icon' => 'fa-solid fa-puzzle-piece' ],
					'wikipress-settings&tab=third-party' => [ 'label' => __( '3rd Party', 'wikipress' ), 'icon' => 'fa-solid fa-plug' ],
					'wikipress-settings&tab=access'      => [ 'label' => __( 'Access', 'wikipress' ), 'icon' => 'fa-solid fa-user-shield' ],
				],
			],
			'tools' => [
				'label' => __( 'Tools', 'wikipress' ),
				'icon'  => 'fa-solid fa-toolbox',
				'items' => [
					'wikipress-tools&tool=debug'     => [ 'label' => __( 'Debug', 'wikipress' ), 'icon' => 'fa-solid fa-bug-slash' ],
					'wikipress-tools&tool=import'    => [ 'label' => __( 'Import', 'wikipress' ), 'icon' => 'fa-solid fa-file-import' ],
					'wikipress-tools&tool=export'    => [ 'label' => __( 'Export', 'wikipress' ), 'icon' => 'fa-solid fa-file-export' ],
					'wikipress-tools&tool=analytics' => [ 'label' => __( 'Analytics', 'wikipress' ), 'icon' => 'fa-solid fa-chart-line' ],
				],
			],
		];
	}

	private static function register_wordpress_menu( array $menu ): void {
		$callback   = $menu['callback'] ?? null;
		$slug       = sanitize_key( (string) ( $menu['slug'] ?? '' ) );
		$name       = (string) ( $menu['name'] ?? '' );
		$parent     = self::admin_parent_slug( (string) ( $menu['parent'] ?? '' ) );
		$capability = sanitize_key( (string) ( $menu['capability'] ?? 'manage_options' ) );

		if ( '' === $slug || '' === $name || ! is_callable( $callback ) ) {
			return;
		}

		if ( '' === $parent ) {
			add_menu_page( $name, $name, $capability, $slug, $callback, $menu['icon'] ?? 'dashicons-admin-generic', $menu['position'] ?? null );
			return;
		}

		add_submenu_page( $parent, $name, $name, $capability, $slug, $callback, $menu['position'] ?? null );
	}

	/** @return array<int, array<string, mixed>> */
	private static function plugin_wordpress_menus(): array {
		$menus = [];

		foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
			if ( ! $plugin instanceof AdminMenuProviderInterface || ! $plugin->is_active() ) {
				continue;
			}

			try {
				foreach ( $plugin->get_admin_menu() as $definition ) {
					if ( ! is_array( $definition ) ) {
						continue;
					}

					$menus[] = self::normalize_wordpress_menu( $definition );
					foreach ( $definition['children'] ?? [] as $child ) {
						if ( is_array( $child ) ) {
							$child['parent'] = $definition['menu_slug'] ?? '';
							$menus[] = self::normalize_wordpress_menu( $child );
						}
					}
				}
			} catch ( \Throwable $e ) {
				LoggerHelper::write_log( sprintf( 'WikiPress plugin %s failed to provide WordPress menus: %s', $plugin->get_slug(), $e->getMessage() ) );
			}
		}

		return array_values( array_filter( $menus, static fn ( $menu ): bool => is_array( $menu ) ) );
	}

	/** @param array<string, mixed> $definition @return array<string, mixed> */
	private static function normalize_wordpress_menu( array $definition ): array {
		return [
			'name'       => $definition['menu_title'] ?? $definition['page_title'] ?? '',
			'slug'       => $definition['menu_slug'] ?? '',
			'icon'       => $definition['icon'] ?? 'dashicons-admin-generic',
			'parent'     => $definition['parent'] ?? '',
			'callback'   => $definition['callback'] ?? null,
			'capability' => $definition['capability'] ?? 'manage_options',
			'position'   => $definition['position'] ?? null,
		];
	}

	private static function admin_parent_slug( string $parent ): string {
		$parent = strtolower( sanitize_text_field( $parent ) );
		return (string) preg_replace( '/[^a-z0-9._-]/', '', $parent );
	}

	/** @return array<int, array<string, mixed>> */
	private static function plugin_sidebar_menus(): array {
		$menus = [];

		foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
			if ( ! $plugin instanceof AdminSidebarProviderInterface || ! $plugin->is_active() ) {
				continue;
			}

			try {
				foreach ( $plugin->get_admin_sidebar() as $definition ) {
					if ( ! is_array( $definition ) ) {
						continue;
					}

					if ( 'group' === ( $definition['type'] ?? '' ) ) {
						$menus[] = WASMHelper::define( $definition['label'] ?? '', $definition['slug'] ?? '', $definition['icon'] ?? '' );
						foreach ( $definition['items'] ?? [] as $child ) {
							if ( is_array( $child ) ) {
								$menus[] = WASMHelper::define( $child['label'] ?? '', self::sidebar_slug( $child ), $child['icon'] ?? '', $definition['slug'] ?? '' );
							}
						}
						continue;
					}

					$menus[] = WASMHelper::define( $definition['label'] ?? '', self::sidebar_slug( $definition ), $definition['icon'] ?? '', $definition['parent'] ?? '' );
				}
			} catch ( \Throwable $e ) {
				LoggerHelper::write_log( sprintf( 'WikiPress plugin %s failed to provide sidebar menus: %s', $plugin->get_slug(), $e->getMessage() ) );
			}
		}

		return $menus;
	}

	/** @param array<string, mixed> $definition */
	private static function sidebar_slug( array $definition ): string {
		$page  = (string) ( $definition['page'] ?? $definition['slug'] ?? '' );
		$query = $definition['query'] ?? [];

		if ( ! is_array( $query ) || empty( $query ) ) {
			return $page;
		}

		return $page . '&' . http_build_query( array_filter( $query, 'is_scalar' ), '', '&', PHP_QUERY_RFC3986 );
	}

	/** @param array<string, array<string, mixed>> $groups @param array<string, mixed> $menu */
	private static function add_sidebar_group( array &$groups, array $menu ): void {
		$slug  = self::menu_slug( $menu );
		$label = (string) ( $menu['name'] ?? '' );
		$icon  = (string) ( $menu['icon'] ?? '' );

		if ( '' !== $slug && '' !== $label && '' !== $icon ) {
			$groups[ $slug ] = [ 'label' => $label, 'icon' => $icon, 'items' => [] ];
		}
	}

	/** @param array<string, array<string, mixed>> $groups @param array<string, mixed> $menu */
	private static function add_sidebar_item( array &$groups, string $parent, array $menu ): void {
		$slug  = (string) ( $menu['slug'] ?? '' );
		$label = (string) ( $menu['name'] ?? '' );
		$icon  = (string) ( $menu['icon'] ?? '' );

		if ( isset( $groups[ $parent ] ) && '' !== $slug && '' !== $label && '' !== $icon ) {
			$groups[ $parent ]['items'][ $slug ] = [ 'label' => $label, 'icon' => $icon ];
		}
	}

	/** @param array<string, mixed> $menu */
	private static function parent_slug( array $menu ): string {
		return sanitize_key( (string) ( $menu['parent'] ?? '' ) );
	}

	/** @param array<string, mixed> $menu */
	private static function menu_slug( array $menu ): string {
		return sanitize_key( (string) ( $menu['slug'] ?? '' ) );
	}
}
