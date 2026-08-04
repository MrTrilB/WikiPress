<?php
/**
 * Renderer for the Wiki Search Modal block.
 *
 * @package TrilBDev\WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiSearchModal {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        return '<div class="wikipress-search-modal"><button type="button" class="wikipress-search-modal__open">' . esc_html__( 'Search Wiki', 'wikipress' ) . '</button><div class="wikipress-search-modal__overlay" hidden><div class="wikipress-search-modal__dialog" role="dialog" aria-modal="true"><button type="button" class="wikipress-search-modal__close" aria-label="' . esc_attr__( 'Close search', 'wikipress' ) . '">×</button><form role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '"><label for="wikipress-block-search">' . esc_html__( 'Search', 'wikipress' ) . '</label><input id="wikipress-block-search" type="search" name="s"><input type="submit" value="' . esc_attr__( 'Search', 'wikipress' ) . '"></form></div></div></div>';
    }
}
