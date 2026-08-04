<?php
/**
 * Docs Search Modal widget template.
 *
 * @package TrilBDev
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;

if ( ! isset( $widget ) || ! $widget instanceof Widget_Base ) {
    return;
}

if ( empty( $context ) || ! is_array( $context ) ) {
    return;
}

$wrapper_classes    = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'trilbdev-docs-searchmodal' ];
$action_url         = isset( $context['action_url'] ) ? (string) $context['action_url'] : home_url( '/' );
$open_button_label  = isset( $context['open_button_label'] ) ? (string) $context['open_button_label'] : __( 'Search Docs', 'trilbdev' );
$search_placeholder = isset( $context['search_placeholder'] ) ? (string) $context['search_placeholder'] : __( 'Search docs...', 'trilbdev' );
$submit_label       = isset( $context['submit_label'] ) ? (string) $context['submit_label'] : __( 'Search', 'trilbdev' );
$close_label        = isset( $context['close_label'] ) ? (string) $context['close_label'] : '×';
$close_aria_label   = __( 'Close search modal', 'trilbdev' );

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wrapper_classes ) ) ) );
?>
<div class="<?php echo $wrapper_class_attr; ?>">
    <button type="button" class="open-search"><?php echo esc_html( $open_button_label ); ?></button>
    <div class="overlay" style="display:none;">
        <div class="inner">
            <button type="button" class="close" aria-label="<?php echo esc_attr( $close_aria_label ); ?>"><?php echo esc_html( $close_label ); ?></button>
            <form role="search" method="get" action="<?php echo esc_url( $action_url ); ?>">
                <input type="hidden" name="post_type" value="docs" />
                <input type="search" name="s" placeholder="<?php echo esc_attr( $search_placeholder ); ?>" />
                <button type="submit"><?php echo esc_html( $submit_label ); ?></button>
            </form>
        </div>
    </div>
</div>
