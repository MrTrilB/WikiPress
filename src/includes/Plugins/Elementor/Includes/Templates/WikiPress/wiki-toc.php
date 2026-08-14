<?php
/**
 * Docs Table of Contents widget template.
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

$wikipress_items          = isset( $context['items'] ) && is_array( $context['items'] ) ? $context['items'] : [];
$wikipress_has_items      = ! empty( $context['has_items'] ) && ! empty( $wikipress_items );
$wikipress_heading        = isset( $context['heading'] ) ? (string) $context['heading'] : __( 'Table of Contents', 'wikipress' );
$wikipress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-toc' ];

if ( ! $wikipress_has_items ) {
    return;
}

?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wikipress_wrapper_classes ) ) ) ); ?>">
    <strong class="docs-toc__heading"><?php echo esc_html( $wikipress_heading ); ?></strong>
    <ul class="docs-toc__list">
        <?php foreach ( $wikipress_items as $wikipress_item ) :
            if ( ! is_array( $wikipress_item ) || empty( $wikipress_item['title'] ) ) {
                continue;
            }

            $wikipress_level = isset( $wikipress_item['level'] ) ? (int) $wikipress_item['level'] : 0;
            $wikipress_title = (string) $wikipress_item['title'];
            $wikipress_id    = isset( $wikipress_item['id'] ) ? (string) $wikipress_item['id'] : '';

            $wikipress_classes = [ 'docs-toc__item' ];
            if ( $wikipress_level >= 2 && $wikipress_level <= 6 ) {
                $wikipress_classes[] = 'level-' . $wikipress_level;
            }
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $wikipress_classes ) ); ?>">
                <a class="docs-toc__link" href="<?php echo esc_url( '#' . $wikipress_id ); ?>"><?php echo esc_html( $wikipress_title ); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
