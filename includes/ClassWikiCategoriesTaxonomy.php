<?php
namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Includes;
use MrTrilB\TrilBDevPlugin\Includes\Functions\utilities;

/**
 * Custom Taxonomy Category for Wiki Post Type
 *
 * This class registers the 'Category' custom taxonomy, which is used to categorize Wiki entries based on the type of change that they are.
 *
 * @package   TrilB.Dev Plugins
 * @subpackage WikiCategoriesTaxonomy
 * @since     1.0.0
 * @version   1.0.0
 * @author    MrTrilB <https://trilb.dev>
 * @license   GPL-2.0-or-later
 */
class WikiCategoriesTaxonomy {

    /**
     * Constructor.
     */
    public function __construct() {
        utilities::write_log( 'WikiCategoriesTaxonomy::__construct() called' );
    }

    public static function get_taxonomy_name(): string {
        return 'wiki-categories';
    }

    public static function get_rest_base(): string {
        return 'wiki-categories';
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
     * Registers the post type.
     */
    public function register_taxonomy() {
        if ( taxonomy_exists( 'wiki-categories' ) ) {
            return 'wiki-categories'; // Return early if already registered
        }

        $labels = array(
            'name'                       => _x( 'Wiki Categories', 'Taxonomy General Name', 'TBD_Translate' ),
            'singular_name'              => _x( 'Wiki Category', 'Taxonomy Singular Name', 'TBD_Translate' ),
            'menu_name'                  => __( 'Categories', 'TBD_Translate' ),
            'all_items'                  => __( 'All Categories', 'TBD_Translate' ),
            'parent_item'                => __( 'Category Archives', 'TBD_Translate' ),
            'parent_item_colon'          => __( 'Parent Category:', 'TBD_Translate' ),
            'new_item_name'              => __( 'New Category Name', 'TBD_Translate' ),
            'add_new_item'               => __( 'Add New Category', 'TBD_Translate' ),
            'edit_item'                  => __( 'Edit Category', 'TBD_Translate' ),
            'update_item'                => __( 'Update Category', 'TBD_Translate' ),
            'view_item'                  => __( 'View Category', 'TBD_Translate' ),
            'separate_items_with_commas' => __( 'Separate Categories with commas', 'TBD_Translate' ),
            'add_or_remove_items'        => __( 'Add or remove Categories', 'TBD_Translate' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'TBD_Translate' ),
            'popular_items'              => __( 'Popular Categories', 'TBD_Translate' ),
            'search_items'               => __( 'Search Categories', 'TBD_Translate' ),
            'not_found'                  => __( 'Not Found', 'TBD_Translate' ),
            'no_terms'                   => __( 'No Categories', 'TBD_Translate' ),
            'items_list'                 => __( 'Items list', 'TBD_Translate' ),
            'items_list_navigation'      => __( 'Items list navigation', 'TBD_Translate' ),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_tagcloud'         => false,
            'show_in_rest'          => true,
            'rest_base'             => 'wiki-categories',
            'rest_controller_class' => 'WP_REST_Terms_Controller', // You might need to adjust this
        );

        register_taxonomy( 'wiki-categories', array( 'wiki' ), $args );

        return 'wiki-categories'; // Return the taxonomy name
    }
}