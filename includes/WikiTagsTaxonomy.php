<?php

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Includes;
use MrTrilB\TrilBDevPlugin\Includes\Functions\utilities;

/**
 * Custom Taxonomy Tag for Wiki Post Type
 *
 * This class registers the 'Tag' custom taxonomy, which is used to categorize Wiki entries based on the type of change that they are.
 *
 * @package   TrilB.Dev Plugins
 * @subpackage WikiTagsTaxonomy
 * @since     1.0.0
 * @version   1.0.0
 * @author    MrTrilB <https://trilb.dev>
 * @license   GPL-2.0-or-later
 */
class WikiTagsTaxonomy {

    /**
     * Constructor.
     */
    public function __construct() {
        utilities::write_log( 'WikiTagsTaxonomy::__construct() called' );
    }

    public static function get_taxonomy_name(): string {
        return 'wiki-tags';
    }

    public static function get_rest_base(): string {
        return 'wiki-tags';
    }

    public static function get_terms( array $args = [] ): array {
        $defaults = [
            'taxonomy'   => self::get_taxonomy_name(),
            'hide_empty' => false,
        ];

        $terms = get_terms( wp_parse_args( $args, $defaults ) );
        if ( is_wp_error( $terms ) ) {
            return [];
        }

        return array_map( static function ( $term ) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'count' => $term->count,
                'description' => $term->description,
            ];
        }, $terms );
    }

    /**
     * Registers the taxonomy.
     */
    public function register_taxonomy() {
        if ( taxonomy_exists( self::get_taxonomy_name() ) ) {
            return self::get_taxonomy_name();
        }

        $labels = array(
            'name'                       => _x( 'Wiki Tags', 'Taxonomy General Name', ' TBD_Translate' ),
            'singular_name'              => _x( 'Wiki Tag', 'Taxonomy Singular Name', ' TBD_Translate' ),
            'menu_name'                  => __( 'Tags', ' TBD_Translate' ),
            'all_items'                  => __( 'All Tags', ' TBD_Translate' ),
            'parent_item'                => __( 'Tag Archives', ' TBD_Translate' ),
            'parent_item_colon'          => __( 'Parent Tag:', ' TBD_Translate' ),
            'new_item_name'              => __( 'New Tag Name', ' TBD_Translate' ),
            'add_new_item'               => __( 'Add Tag Item', ' TBD_Translate' ),
            'edit_item'                  => __( 'Edit Tag', ' TBD_Translate' ),
            'update_item'                => __( 'Update Tag', ' TBD_Translate' ),
            'view_item'                  => __( 'View Tag', ' TBD_Translate' ),
            'separate_items_with_commas' => __( 'Separate Tags with commas', ' TBD_Translate' ),
            'add_or_remove_items'        => __( 'Add or remove Tags', ' TBD_Translate' ),
            'choose_from_most_used'      => __( 'Choose from the most used', ' TBD_Translate' ),
            'popular_items'              => __( 'Popular Tags', ' TBD_Translate' ),
            'search_items'               => __( 'Search Tags', ' TBD_Translate' ),
            'not_found'                  => __( 'Not Found', ' TBD_Translate' ),
            'no_terms'                   => __( 'No Tags', ' TBD_Translate' ),
            'items_list'                 => __( 'Tags list', ' TBD_Translate' ),
            'items_list_navigation'      => __( 'Tags list navigation', ' TBD_Translate' ),
        );
        
        
        $args = array(
            'labels'                => $labels,
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,  // Hidden since managed through custom mod manager
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_tagcloud'         => true,


            'show_in_rest'          => true,
            'rest_base'             => 'wiki-tags',
            'rest_controller_class' => 'WP_REST_Terms_Controller',

        );
        register_taxonomy( 'wiki-tags', array( 'wiki' ), $args );

        return 'wiki-tags'; // Return the taxonomy name

    }
}