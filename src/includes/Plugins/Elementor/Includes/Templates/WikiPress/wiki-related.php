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

$wikipress_posts           = isset( $context['posts'] ) && is_array( $context['posts'] ) ? $context['posts'] : [];
$wikipress_has_posts       = ! empty( $context['has_posts'] ) && ! empty( $wikipress_posts );
$wikipress_heading         = isset( $context['heading'] ) ? (string) $context['heading'] : __( 'Related Docs', 'wikipress' );
$wikipress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-related-docs' ];

if ( ! $wikipress_has_posts ) {
    return;
}
?>
<section class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wikipress_wrapper_classes ) ) ) ); ?>">
    <h3 class="related-docs__heading"><?php echo esc_html( $wikipress_heading ); ?></h3>
    <?php $wikipress_views_label = esc_attr__( 'View count', 'wikipress' ); ?>
    <ul class="related-docs__list">
        <?php foreach ( $wikipress_posts as $wikipress_post_item ) :
            if ( ! is_array( $wikipress_post_item ) ) {
                continue;
            }

            $wikipress_title     = isset( $wikipress_post_item['title'] ) ? (string) $wikipress_post_item['title'] : '';
            $wikipress_permalink = isset( $wikipress_post_item['permalink'] ) ? (string) $wikipress_post_item['permalink'] : '';
            $wikipress_views     = isset( $wikipress_post_item['views'] ) ? (int) $wikipress_post_item['views'] : 0;

            if ( '' === $wikipress_title ) {
                continue;
            }
            ?>
            <li class="related-docs__item">
                <a class="related-docs__link" href="<?php echo esc_url( $wikipress_permalink ); ?>"><?php echo esc_html( $wikipress_title ); ?></a>
                <span class="related-docs__views" aria-label="<?php echo $wikipress_views_label; ?>">
                    (<?php echo esc_html( number_format_i18n( $wikipress_views ) ); ?>)
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
