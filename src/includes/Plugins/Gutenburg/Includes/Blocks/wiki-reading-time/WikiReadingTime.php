<?php
/**
 * Renderer for the Wiki Reading Time block.
 *
 * @package WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use TrilBDev\WikiPress\Includes\Functions\Helpers\PostHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiReadingTime {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $post = PostHelper::current();
        if ( ! $post ) {
            return '';
        }

        $words = str_word_count( wp_strip_all_tags( (string) $post->post_content ) );
        $minutes = max( 1, (int) ceil( $words / 200 ) );
        /* translators: %d is the estimated reading time in minutes. */
        return '<p class="wikipress-reading-time">' . esc_html( sprintf( _n( '%d minute read', '%d minute read', $minutes, 'wikipress' ), $minutes ) ) . '</p>';
    }
}
