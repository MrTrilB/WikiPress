<?php
/**
 * Docs Reading Time widget template.
 *
 * @package WikiPress
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

if ( empty( $context['has_content'] ) ) {
    return;
}

$wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-reading-time' ];
$display_text    = isset( $context['display_text'] ) ? (string) $context['display_text'] : '';

if ( '' === $display_text ) {
    return;
}

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wrapper_classes ) ) ) );
?>
<div class="<?php echo $wrapper_class_attr; ?>"><?php echo esc_html( $display_text ); ?></div>
