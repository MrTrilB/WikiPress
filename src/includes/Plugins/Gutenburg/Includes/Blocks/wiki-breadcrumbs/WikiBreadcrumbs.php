<?php
/**
 * Renderer for the Wiki Breadcrumbs block.
 *
 * @package WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use TrilBDev\WikiPress\Includes\Functions\Helpers\PostHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiBreadcrumbs {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $post = PostHelper::current();
        if ( ! $post ) {
            return '';
        }

        $label = get_the_title( $post );
        return '<nav class="wikipress-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'wikipress' ) . '"><ol><li class="wikipress-breadcrumbs__item"><span aria-current="page">' . esc_html( $label ) . '</span></li></ol></nav>';
    }
}
