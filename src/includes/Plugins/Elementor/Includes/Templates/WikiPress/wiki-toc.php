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

$items           = isset( $context['items'] ) && is_array( $context['items'] ) ? $context['items'] : [];
$has_items       = ! empty( $context['has_items'] ) && ! empty( $items );
    $heading         = isset( $context['heading'] ) ? (string) $context['heading'] : __( 'Table of Contents', 'wikipress' );
    $wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-toc' ];

if ( ! $has_items ) {
    return;
}

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wrapper_classes ) ) ) );
?>
<div class="<?php echo $wrapper_class_attr; ?>">
    <strong class="docs-toc__heading"><?php echo esc_html( $heading ); ?></strong>
    <ul class="docs-toc__list">
        <?php foreach ( $items as $item ) :
            if ( ! is_array( $item ) || empty( $item['title'] ) ) {
                continue;
            }

            $level = isset( $item['level'] ) ? (int) $item['level'] : 0;
            $title = (string) $item['title'];
            $id    = isset( $item['id'] ) ? (string) $item['id'] : '';

            $classes = [ 'docs-toc__item' ];
            if ( $level >= 2 && $level <= 6 ) {
                $classes[] = 'level-' . $level;
            }
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
                <a class="docs-toc__link" href="<?php echo esc_url( '#' . $id ); ?>"><?php echo esc_html( $title ); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
