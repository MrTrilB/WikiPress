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

$wikipress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-reading-time' ];
$wikipress_display_text   = isset( $context['display_text'] ) ? (string) $context['display_text'] : '';

if ( '' === $wikipress_display_text ) {
    return;
}
?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wikipress_wrapper_classes ) ) ) ); ?>"><?php echo esc_html( $wikipress_display_text ); ?></div>
