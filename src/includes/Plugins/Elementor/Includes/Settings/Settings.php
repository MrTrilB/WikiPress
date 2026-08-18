<?php
/**
 * Settings for the WikiPress Elementor integration.
 *
 * @package WikiPress
 * @subpackage Includes\Plugins\Elementor\Includes\Settings
 * @since 1.0.0
 */
namespace WikiPress\Includes\Plugins\Elementor\Includes\Settings;

use WikiPress\Includes\Settings\Settings as BaseSettings;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;

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
			'layout' => 'box',
			'fields' => [
				[ 'key' => 'elementor_enabled', 'label' => __( 'Enable WikiPress Elementor widgets', 'wikipress' ), 'description' => __( 'Enable the WikiPress widgets available in Elementor.', 'wikipress' ), 'tooltip' => __( 'Disable this to remove WikiPress widgets from the Elementor editor.', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_breadcrumbs', 'label' => __( 'Wiki Breadcrumbs', 'wikipress' ), 'description' => __( 'Show the current wiki location and hierarchy.', 'wikipress' ), 'tooltip' => __( 'Adds breadcrumb navigation to Elementor layouts.', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_list', 'label' => __( 'Wiki List', 'wikipress' ), 'description' => __( 'Show a list of WikiPress content.', 'wikipress' ), 'tooltip' => __( 'Use this widget to display wiki entries in an Elementor layout.', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_reading_time', 'label' => __( 'Wiki Reading Time', 'wikipress' ), 'description' => __( 'Show the estimated reading time for a wiki page.', 'wikipress' ), 'tooltip' => __( 'The estimate is based on the page content.', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_related', 'label' => __( 'Wiki Related', 'wikipress' ), 'description' => __( 'Show related wiki content.', 'wikipress' ), 'tooltip' => __( 'Related content helps visitors continue exploring the wiki.', 'wikipress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_toc', 'label' => __( 'Wiki Table of Contents', 'wikipress' ), 'description' => __( 'Show a table of contents for wiki page headings.', 'wikipress' ), 'tooltip' => __( 'The table of contents is generated from headings in the page.', 'wikipress' ), 'tooltip_type' => 'info', 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_search_modal', 'label' => __( 'Wiki Search Modal', 'wikipress' ), 'description' => __( 'Add a modal search interface for wiki content.', 'wikipress' ), 'tooltip' => __( 'Visitors can open the modal from the widget and search without leaving the page.', 'wikipress' ), 'default' => true ],
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
