<?php
/**
 * Settings for the WikiPress Gutenberg blocks.
 *
 * @package TrilBDev\WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Settings;

use TrilBDev\WikiPress\Includes\Settings\Settings as BaseSettings;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
    public const GROUP = 'gutenburg';

    public function register(): void {
        BaseSettings::register_group( self::GROUP, [
            'gutenburg_enabled' => true,
            'gutenburg_block_wiki_breadcrumbs' => true,
            'gutenburg_block_wiki_list' => true,
            'gutenburg_block_wiki_reading_time' => true,
            'gutenburg_block_wiki_related' => true,
            'gutenburg_block_wiki_toc' => true,
            'gutenburg_block_wiki_search_modal' => true,
        ] );
    }

    public static function enabled(): bool {
        return BaseSettings::get_bool( 'gutenburg_enabled', true );
    }

    public static function block_enabled( string $slug ): bool {
        return self::enabled() && BaseSettings::get_bool( 'gutenburg_block_' . SanitizationHelper::key( $slug ), true );
    }

    public function get_settings_page(): array {
        return [
            'slug' => self::GROUP,
            'label' => __( 'Gutenberg', 'wikipress' ),
            'title' => __( 'Gutenberg integration', 'wikipress' ),
            'fields' => [
                [ 'key' => 'gutenburg_enabled', 'label' => __( 'Enable WikiPress Gutenberg blocks', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_breadcrumbs', 'label' => __( 'Wiki Breadcrumbs', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_list', 'label' => __( 'Wiki List', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_reading_time', 'label' => __( 'Wiki Reading Time', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_related', 'label' => __( 'Wiki Related', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_toc', 'label' => __( 'Wiki Table of Contents', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_search_modal', 'label' => __( 'Wiki Search Modal', 'wikipress' ), 'default' => true ],
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