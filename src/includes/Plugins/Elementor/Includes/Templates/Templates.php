<?php
/**
 * 
 * WikiPress Elementor Widget Template Integration Class
 * 
 * A Class that handles the integration of the WikiPress plugin with Elementor.
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes/Elementor/
 * @since 1.0.0
 */
namespace WikiPress\Includes\Plugins\Elementor\Includes\Templates;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Templates {
    public static function render( string $template, array $args = [] ): void {
        $template = ltrim( $template, '/' );
        if ( str_starts_with( $template, 'widgets/wiki/' ) ) {
            $template = 'WikiPress/wiki-' . substr( $template, strlen( 'widgets/wiki/' ) );
        }

        $path = dirname( __DIR__ ) . '/Templates/' . $template . '.php';
        if ( ! is_readable( $path ) ) {
            return;
        }

        extract( $args, EXTR_SKIP );
        include $path;
    }
}