<?php
/**
 * Settings for the WikiPress Elementor integration.
 *
 * @package TrilBDev\WikiPress
 * @subpackage Includes\Plugins\Elementor\Includes\Settings
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Settings;

use TrilBDev\WikiPress\Includes\Settings\Settings as BaseSettings;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Settings {
	public const GROUP = 'elementor';

	public function register(): void {
		BaseSettings::register_group( self::GROUP, [
			'elementor_enabled' => true,
			'elementor_widget_wiki_breadcrumbs' => true,
			'elementor_widget_wiki_list' => true,
			'elementor_widget_wiki_reading_time' => true,
			'elementor_widget_wiki_related' => true,
			'elementor_widget_wiki_toc' => true,
			'elementor_widget_wiki_search_modal' => true,
		] );
	}

	public static function enabled(): bool {
		return BaseSettings::get_bool( 'elementor_enabled', true );
	}

	public static function widget_enabled( string $slug ): bool {
		return self::enabled() && BaseSettings::get_bool( 'elementor_widget_' . SanitizationHelper::key( $slug ), true );
	}

	public function get_settings_page(): array {
		return [
			'slug' => self::GROUP,
			'label' => __( 'Elementor', 'wikipress' ),
			'title' => __( 'Elementor integration', 'wikipress' ),
			'fields' => [
				[ 'key' => 'elementor_enabled', 'label' => __( 'Enable WikiPress Elementor widgets', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_breadcrumbs', 'label' => __( 'Wiki Breadcrumbs', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_list', 'label' => __( 'Wiki List', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_reading_time', 'label' => __( 'Wiki Reading Time', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_related', 'label' => __( 'Wiki Related', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_toc', 'label' => __( 'Wiki Table of Contents', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_search_modal', 'label' => __( 'Wiki Search Modal', 'wikipress' ), 'default' => true ],
			],
		];
	}

	public function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : [];
		foreach ( array_column( $this->get_settings_page()['fields'], 'key' ) as $key ) {
			$input[ $key ] = ! empty( $input[ $key ] );
			BaseSettings::set( $key, $input[ $key ] );
		}
		return $input;
	}
}
