<?php
/**
 * Renderer for the Wiki Related block.
 *
 * @package WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use TrilBDev\WikiPress\Includes\Functions\Helpers\PostHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\QueryHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiRelated {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $post_id = PostHelper::current_id();
        if ( ! $post_id ) {
            return '';
        }

        $limit = max( 1, min( 12, absint( $attributes['limit'] ?? 5 ) ) );
        $query = QueryHelper::posts([
            'post_type' => 'wiki',
            'post__not_in' => [ $post_id ],
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        if ( ! $query->have_posts() ) {
            return '';
        }

        $items = [];
        while ( $query->have_posts() ) {
            $query->the_post();
            $items[] = '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
        }
        wp_reset_postdata();
        return '<section class="wikipress-related"><h3>' . esc_html__( 'Related Wiki Posts', 'wikipress' ) . '</h3><ul>' . implode( '', $items ) . '</ul></section>';
    }
}
