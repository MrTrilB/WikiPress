<?php
namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Includes;
use MrTrilB\TrilBDevPlugin\Includes\Functions\utilities;

/**
 * Custom Post Type for Wiki Post Type
 *
 * This class registers the 'Wiki' custom post type, which is used to manage Wiki entries.
 *
 * @package   TrilB.Dev Plugins
 * @subpackage WikiPostType
 * @since     1.0.0
 * @version   1.0.0
 * @author    MrTrilB <https://trilb.dev>
 * @license   GPL-2.0-or-later
 */
class WikiPostType {

    /**
     * Constructor.
     */
    public function __construct() {
        utilities::write_log( 'WikiPostType: Starting registration' );
    }

    /**
     * Returns the post type name.
     */
    public static function get_post_type_name(): string {
        return 'wiki';
    }

    /**
     * Registers the post type.
     */
    public function register_post_type() {
        if ( post_type_exists( self::get_post_type_name() ) ) {
            return self::get_post_type_name();
        }

        $labels = array(
            'name'                  => _x( 'Wiki Posts', 'Post Type General Name', ' TBD_Translate' ),
            'singular_name'         => _x( 'Wiki Post', 'Post Type Singular Name', ' TBD_Translate' ),
            'menu_name'             => __( 'Wiki', ' TBD_Translate' ),
            'name_admin_bar'        => __( 'Wiki', ' TBD_Translate' ),
            'archives'              => __( 'Wiki Archives', ' TBD_Translate' ),
            'attributes'            => __( 'Wiki Attributes', ' TBD_Translate' ),
            'parent_item_colon'     => __( 'Parent Wiki Post:', ' TBD_Translate' ),
            'all_items'             => __( 'All Posts', ' TBD_Translate' ),
            'add_new_item'          => __( 'Add New Posts', ' TBD_Translate' ),
            'add_new'               => __( 'Add New', ' TBD_Translate' ),
            'new_item'              => __( 'New Wiki Post', ' TBD_Translate' ),
            'edit_item'             => __( 'Edit Wiki Post', ' TBD_Translate' ),
            'update_item'           => __( 'Update Wiki Post', ' TBD_Translate' ),
            'view_item'             => __( 'View Wiki Post', ' TBD_Translate' ),
            'view_items'            => __( 'View Wiki Posts', ' TBD_Translate' ),
            'search_items'          => __( 'Search Wiki Posts', ' TBD_Translate' ),
            'not_found'             => __( 'Not Found', ' TBD_Translate' ),
            'not_found_in_trash'    => __( 'Not Found in Trash', ' TBD_Translate' ),
            'featured_image'        => __( 'Featured Image', ' TBD_Translate' ),
            'set_featured_image'    => __( 'Set featured image', ' TBD_Translate' ),
            'remove_featured_image' => __( 'Remove featured image', ' TBD_Translate' ),
            'use_featured_image'    => __( 'Use as featured image', ' TBD_Translate' ),
            'insert_into_item'      => __( 'Insert into Wiki Post', ' TBD_Translate' ),
            'uploaded_to_this_item' => __( 'Uploaded to this Wiki Post', ' TBD_Translate' ),
            'items_list'            => __( 'Wiki Posts list', ' TBD_Translate' ),
            'items_list_navigation' => __( 'Wiki Posts list navigation', ' TBD_Translate' ),
            'filter_items_list'     => __( 'Filter Wiki Posts list', ' TBD_Translate' ),
        );
        
        
        $rewrite_options = array(
            'slug'       => 'wiki',
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        ); 
                
        $args = array(
            'label'                 => __( 'Wiki', ' TBD_Translate' ),
            'description'           => __( 'For curating and displaying the Wiki posts i create about all aspects of the world of Warhammer', ' TBD_Translate' ),
            'labels'                => $labels,
            'supports'              => $this->get_supported_features(),
            'taxonomies'            => $this->get_taxonomies(),
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 60,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'rewrite'               => $rewrite_options,
            'menu_icon'             => 'dashicons-admin-customizer',
            'show_in_rest'          => true,
            'rest_base'             => 'wiki',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type( self::get_post_type_name(), $args );

        return self::get_post_type_name();
    }

    private function get_supported_features(): array {
        return [
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'revisions',
            'page-attributes',
        ];
    }

    private function get_taxonomies(): array {
        return [
            'wiki-categories',
            'wiki-tags',
            'multi-wiki',
        ];
    }
}
