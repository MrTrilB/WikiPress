<?php
/**
 * Docs Search Modal widget template.
 *
 * @package WikiPress
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! isset( $widget ) || ! $widget instanceof Widget_Base ) {
    return;
}

if ( empty( $context ) || ! is_array( $context ) ) {
    return;
}

    $wrapper_classes    = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-searchmodal' ];
$action_url         = isset( $context['action_url'] ) ? (string) $context['action_url'] : home_url( '/' );
    $open_button_label  = isset( $context['open_button_label'] ) ? (string) $context['open_button_label'] : __( 'Search Docs', 'wikipress' );
    $search_placeholder = isset( $context['search_placeholder'] ) ? (string) $context['search_placeholder'] : __( 'Search docs...', 'wikipress' );
$submit_label       = isset( $context['submit_label'] ) ? (string) $context['submit_label'] : __( 'Search', 'wikipress' );
$close_label        = isset( $context['close_label'] ) ? (string) $context['close_label'] : '×';
$close_aria_label   = __( 'Close search modal', 'wikipress' );

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wrapper_classes ) ) ) );
?>
<div class="<?php echo $wrapper_class_attr; ?>">
    <button type="button" class="open-search"><?php echo esc_html( $open_button_label ); ?></button>
    <div class="overlay" style="display:none;">
        <div class="inner">
            <button type="button" class="close" aria-label="<?php echo esc_attr( $close_aria_label ); ?>"><?php echo esc_html( $close_label ); ?></button>
            <form role="search" method="get" action="<?php echo esc_url( $action_url ); ?>">
                <?php echo wp_kses_post( FormFieldHelper::input( 'post_type', 'docs', [ 'type' => 'hidden' ] ) ); ?>
                <?php echo wp_kses_post( FormFieldHelper::input( 's', '', [ 'type' => 'search', 'placeholder' => $search_placeholder ] ) ); ?>
                <button type="submit"><?php echo esc_html( $submit_label ); ?></button>
            </form>
        </div>
    </div>
</div>
