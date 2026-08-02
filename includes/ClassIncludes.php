<?php
/**
 * TrilB.Dev Plugin - Wiki Includes
 *
 * Coordinates Wiki post type and taxonomy registration and provides
 * shared helpers for the Wiki module.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Includes
 * @since 1.0.0
 */
namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Includes;

use MrTrilB\TrilBDevPlugin\Includes\Functions\utilities;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Includes {
    private static ?Includes $instance = null;
    private array $taxonomies = [];

    private function __construct() {
        utilities::write_log( 'Wiki Includes: Initializing include bootstrap.' );
        $this->taxonomies = [
            WikiCategoriesTaxonomy::class,
            WikiTagsTaxonomy::class,
            MultiWikiTaxonomy::class,
        ];
    }

    public static function get_instance(): Includes {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void {
        $this->register_post_type();
        $this->register_taxonomies();
    }

    public function register_post_type(): string {
        require_once TRILBDEV_INCLUDES . '/Wiki/Includes/WikiPostType.php';

        $post_type = new WikiPostType();
        return $post_type->register_post_type();
    }

    public function register_taxonomies(): array {
        require_once TRILBDEV_INCLUDES . '/Wiki/Includes/WikiCategoriesTaxonomy.php';
        require_once TRILBDEV_INCLUDES . '/Wiki/Includes/WikiTagsTaxonomy.php';
        require_once TRILBDEV_INCLUDES . '/Wiki/Includes/MultiWikiTaxonomy.php';

        $registered = [];
        foreach ( $this->taxonomies as $taxonomy_class ) {
            if ( ! class_exists( $taxonomy_class ) ) {
                continue;
            }

            $instance = new $taxonomy_class();
            if ( method_exists( $instance, 'register_taxonomy' ) ) {
                $name = $instance->register_taxonomy();
                if ( is_string( $name ) && $name !== '' ) {
                    $registered[] = $name;
                }
            }
        }

        return $registered;
    }

    public function get_post_type_name(): string {
        return WikiPostType::get_post_type_name();
    }

    public function get_taxonomy_names(): array {
        return array_filter( array_map( static function ( $taxonomy_class ) {
            if ( method_exists( $taxonomy_class, 'get_taxonomy_name' ) ) {
                return $taxonomy_class::get_taxonomy_name();
            }
            return '';
        }, $this->taxonomies ) );
    }

    public function get_taxonomy_terms( string $taxonomy_name, array $args = [] ): array {
        foreach ( $this->taxonomies as $taxonomy_class ) {
            if ( method_exists( $taxonomy_class, 'get_taxonomy_name' ) && $taxonomy_class::get_taxonomy_name() === $taxonomy_name ) {
                return method_exists( $taxonomy_class, 'get_terms' ) ? $taxonomy_class::get_terms( $args ) : [];
            }
        }

        return [];
    }
}
