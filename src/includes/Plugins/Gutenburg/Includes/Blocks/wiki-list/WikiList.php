<?php
/**
 * Renderer for the Wiki List block.
 *
 * @package WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use TrilBDev\WikiPress\Includes\Functions\Helpers\QueryHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiList {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $per_page = max( 1, min( 50, absint( $attributes['perPage'] ?? 10 ) ) );
        $query = QueryHelper::posts([
            'post_type' => 'wiki',
            'posts_per_page' => $per_page,
            'orderby' => in_array( $attributes['orderby'] ?? 'date', [ 'date', 'title' ], true ) ? $attributes['orderby'] : 'date',
            'order' => 'ASC' === ( $attributes['order'] ?? '' ) ? 'ASC' : 'DESC',
        ]);

        if ( ! $query->have_posts() ) {
            return '<p class="wikipress-wiki-list__empty">' . esc_html__( 'No wiki posts found.', 'wikipress' ) . '</p>';
        }

        $items = [];
        while ( $query->have_posts() ) {
            $query->the_post();
            $items[] = sprintf(
                '<article class="wikipress-wiki-list__item"><h3><a href="%s">%s</a></h3><div>%s</div></article>',
                esc_url( get_permalink() ),
                esc_html( get_the_title() ),
                wp_kses_post( get_the_excerpt() )
            );
        }
        wp_reset_postdata();

        return '<div class="wikipress-wiki-list">' . implode( '', $items ) . '</div>';
    }
}
