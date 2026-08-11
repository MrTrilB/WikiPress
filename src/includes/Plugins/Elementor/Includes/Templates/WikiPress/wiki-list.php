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

$posts            = isset( $context['posts'] ) && is_array( $context['posts'] ) ? $context['posts'] : [];
$has_posts        = ! empty( $context['has_posts'] ) && ! empty( $posts );
$pagination       = isset( $context['pagination'] ) ? (string) $context['pagination'] : '';
$no_results       = isset( $context['no_results_message'] ) ? (string) $context['no_results_message'] : __( 'No docs found.', 'wikipress' );
$wrapper_classes  = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-list-widget' ];

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $wrapper_classes ) ) ) );
?>
<div class="<?php echo $wrapper_class_attr; ?>">
    <?php if ( $has_posts ) : ?>
        <div class="docs-items">
            <?php foreach ( $posts as $post_item ) :
                if ( ! is_array( $post_item ) ) {
                    continue;
                }

                $title     = isset( $post_item['title'] ) ? (string) $post_item['title'] : '';
                $permalink = isset( $post_item['permalink'] ) ? (string) $post_item['permalink'] : '';
                $excerpt   = isset( $post_item['excerpt'] ) ? (string) $post_item['excerpt'] : '';

                if ( '' === $title ) {
                    continue;
                }
                ?>
                <article class="docs-item">
                    <h3 class="docs-item__title">
                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                    </h3>
                    <?php if ( '' !== $excerpt ) : ?>
                        <div class="docs-item__excerpt"><?php echo wp_kses_post( $excerpt ); ?></div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ( '' !== $pagination ) : ?>
            <div class="docs-pagination"><?php echo wp_kses_post( $pagination ); ?></div>
        <?php endif; ?>
    <?php else : ?>
        <p class="docs-no-results"><?php echo esc_html( $no_results ); ?></p>
    <?php endif; ?>
</div>
