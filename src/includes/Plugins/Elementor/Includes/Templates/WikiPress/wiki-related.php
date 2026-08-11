<?php
/**
 * Docs Related widget template.
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

$posts           = isset( $context['posts'] ) && is_array( $context['posts'] ) ? $context['posts'] : [];
$has_posts       = ! empty( $context['has_posts'] ) && ! empty( $posts );
    $heading         = isset( $context['heading'] ) ? (string) $context['heading'] : __( 'Related Docs', 'wikipress' );
    $wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-related-docs' ];

if ( ! $has_posts ) {
    return;
}

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wrapper_classes ) ) ) );
?>
<section class="<?php echo $wrapper_class_attr; ?>">
    <h3 class="related-docs__heading"><?php echo esc_html( $heading ); ?></h3>
    <?php $views_label = esc_attr__( 'View count', 'wikipress' ); ?>
    <ul class="related-docs__list">
        <?php foreach ( $posts as $post_item ) :
            if ( ! is_array( $post_item ) ) {
                continue;
            }

            $title     = isset( $post_item['title'] ) ? (string) $post_item['title'] : '';
            $permalink = isset( $post_item['permalink'] ) ? (string) $post_item['permalink'] : '';
            $views     = isset( $post_item['views'] ) ? (int) $post_item['views'] : 0;

            if ( '' === $title ) {
                continue;
            }
            ?>
            <li class="related-docs__item">
                <a class="related-docs__link" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                <span class="related-docs__views" aria-label="<?php echo $views_label; ?>">
                    (<?php echo esc_html( number_format_i18n( $views ) ); ?>)
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
