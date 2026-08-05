<?php
/**
 * Renderer for the Wiki Search Modal block.
 *
 * @package TrilBDev\WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiSearchModal {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $search = FormFieldHelper::input( 's', '', [ 'type' => 'search', 'id' => 'wikipress-block-search' ] );
        $submit = FormFieldHelper::input( 'submit', __( 'Search', 'wikipress' ), [ 'type' => 'submit' ] );
        return '<div class="wikipress-search-modal"><button type="button" class="wikipress-search-modal__open">' . esc_html__( 'Search Wiki', 'wikipress' ) . '</button><div class="wikipress-search-modal__overlay" hidden><div class="wikipress-search-modal__dialog" role="dialog" aria-modal="true"><button type="button" class="wikipress-search-modal__close" aria-label="' . esc_attr__( 'Close search', 'wikipress' ) . '">×</button><form role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">' . FormFieldHelper::label( 'wikipress-block-search', __( 'Search', 'wikipress' ) ) . $search . $submit . '</form></div></div></div>';
    }
}
