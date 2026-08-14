<?php
/**
 * Docs List widget template.
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
$wikipress_pagination      = isset( $context['pagination'] ) ? (string) $context['pagination'] : '';
$wikipress_no_results      = isset( $context['no_results_message'] ) ? (string) $context['no_results_message'] : __( 'No docs found.', 'wikipress' );
$wikipress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-list-widget' ];
?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wikipress_wrapper_classes ) ) ) ); ?>">
    <?php if ( $has_posts ) : ?>
        <div class="docs-items">
            <?php foreach ( $wikipress_posts as $wikipress_post_item ) :
                if ( ! is_array( $wikipress_post_item ) ) {
                    continue;
                }

                $wikipress_title     = isset( $wikipress_post_item['title'] ) ? (string) $wikipress_post_item['title'] : '';
                $wikipress_permalink = isset( $wikipress_post_item['permalink'] ) ? (string) $wikipress_post_item['permalink'] : '';
                $wikipress_excerpt   = isset( $wikipress_post_item['excerpt'] ) ? (string) $wikipress_post_item['excerpt'] : '';

                if ( '' === $wikipress_title ) {
                    continue;
                }
                ?>
                <article class="docs-item">
                    <h3 class="docs-item__title">
                        <a href="<?php echo esc_url( $wikipress_permalink ); ?>"><?php echo esc_html( $wikipress_title ); ?></a>
                    </h3>
                    <?php if ( '' !== $wikipress_excerpt ) : ?>
                        <div class="docs-item__excerpt"><?php echo wp_kses_post( $wikipress_excerpt ); ?></div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ( '' !== $wikipress_pagination ) : ?>
            <div class="docs-pagination"><?php echo wp_kses_post( $wikipress_pagination ); ?></div>
        <?php endif; ?>
    <?php else : ?>
        <p class="docs-no-results"><?php echo esc_html( $wikipress_no_results ); ?></p>
    <?php endif; ?>
</div>
