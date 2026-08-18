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
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! isset( $widget ) || ! $widget instanceof Widget_Base ) {
    return;
}

if ( empty( $context ) || ! is_array( $context ) ) {
    return;
}

$wikipress_wrapper_classes   = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-searchmodal' ];
$wikipress_action_url        = isset( $context['action_url'] ) ? (string) $context['action_url'] : home_url( '/' );
$wikipress_open_button_label = isset( $context['open_button_label'] ) ? (string) $context['open_button_label'] : __( 'Search Docs', 'wikipress' );
$wikipress_search_placeholder = isset( $context['search_placeholder'] ) ? (string) $context['search_placeholder'] : __( 'Search docs...', 'wikipress' );
$wikipress_submit_label      = isset( $context['submit_label'] ) ? (string) $context['submit_label'] : __( 'Search', 'wikipress' );
$wikipress_close_label       = isset( $context['close_label'] ) ? (string) $context['close_label'] : '×';
$wikipress_close_aria_label  = __( 'Close search modal', 'wikipress' );
?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wikipress_wrapper_classes ) ) ) ); ?>">
    <button type="button" class="open-search"><?php echo esc_html( $wikipress_open_button_label ); ?></button>
    <div class="overlay" style="display:none;">
        <div class="inner">
            <button type="button" class="close" aria-label="<?php echo esc_attr( $wikipress_close_aria_label ); ?>"><?php echo esc_html( $wikipress_close_label ); ?></button>
            <form role="search" method="get" action="<?php echo esc_url( $wikipress_action_url ); ?>">
                <?php echo wp_kses_post( FormFieldHelper::input( 'post_type', 'docs', [ 'type' => 'hidden' ] ) ); ?>
                <?php echo wp_kses_post( FormFieldHelper::input( 's', '', [ 'type' => 'search', 'placeholder' => $wikipress_search_placeholder ] ) ); ?>
                <button type="submit"><?php echo esc_html( $wikipress_submit_label ); ?></button>
            </form>
        </div>
    </div>
</div>
