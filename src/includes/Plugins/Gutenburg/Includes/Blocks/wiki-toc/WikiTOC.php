<?php
/**
 * Renderer for the Wiki Table of Contents block.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use WikiPress\Includes\Functions\Helpers\PostHelper;
use WikiPress\Includes\Functions\Helpers\ContentHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiTOC {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $post = PostHelper::current();
        if ( ! $post ) {
            return '';
        }

        preg_match_all( '/<h([2-6])[^>]*>(.*?)<\/h\\1>/is', (string) $post->post_content, $matches, PREG_SET_ORDER );
        if ( empty( $matches ) ) {
            return '';
        }

        $items = [];
        foreach ( $matches as $match ) {
            $label = ContentHelper::plain_text( $match[2] );
            $id = ContentHelper::heading_id( $label );
            $items[] = '<li class="level-' . absint( $match[1] ) . '"><a href="#' . esc_attr( $id ) . '">' . esc_html( $label ) . '</a></li>';
        }
        return '<nav class="wikipress-toc" aria-label="' . esc_attr__( 'Table of contents', 'wikipress' ) . '"><ul>' . implode( '', $items ) . '</ul></nav>';
    }
}
