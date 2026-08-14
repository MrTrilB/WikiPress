<?php
/**
 * Docs Breadcrumbs widget template.
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

$wikipress_crumbs          = isset( $context['crumbs'] ) && is_array( $context['crumbs'] ) ? $context['crumbs'] : [];
$wikipress_delimiter       = isset( $context['delimiter'] ) ? (string) $context['delimiter'] : ' / ';
$wikipress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'wikipress-docs-breadcrumbs' ];
$wikipress_aria_label      = isset( $context['nav_aria_label'] ) ? (string) $context['nav_aria_label'] : __( 'Breadcrumbs', 'wikipress' );

if ( empty( $wikipress_crumbs ) ) {
    return;
}

$wikipress_sanitize_class = static function ( $class ) {
    $class = is_string( $class ) ? $class : '';

    $class = sanitize_html_class( $class );

    return '' !== $class ? $class : null;
};

$wikipress_crumb_count = count( $wikipress_crumbs );
?>
<nav class="<?php echo esc_attr( implode( ' ', array_filter( array_map( $wikipress_sanitize_class, $wikipress_wrapper_classes ) ) ) ); ?>" aria-label="<?php echo esc_attr( $wikipress_aria_label ); ?>">
    <ol class="wikipress-breadcrumb-list">
        <?php foreach ( $wikipress_crumbs as $wikipress_index => $wikipress_crumb ) :
            if ( ! is_array( $wikipress_crumb ) || empty( $wikipress_crumb['label'] ) ) {
                continue;
            }

            $wikipress_label      = esc_html( (string) $wikipress_crumb['label'] );
            $wikipress_url        = isset( $wikipress_crumb['url'] ) ? (string) $wikipress_crumb['url'] : '';
            $wikipress_is_current = ! empty( $wikipress_crumb['is_current'] );

            $wikipress_item_classes = [ 'breadcrumb-item' ];
            if ( $wikipress_is_current ) {
                $wikipress_item_classes[] = 'is-current';
            }
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $wikipress_item_classes ) ); ?>">
                <?php if ( $wikipress_is_current || '' === $wikipress_url ) : ?>
                    <span class="breadcrumb-label"<?php echo $wikipress_is_current ? ' aria-current="page"' : ''; ?>><?php echo $wikipress_label; ?></span>
                <?php else : ?>
                    <a class="breadcrumb-link" href="<?php echo esc_url( $wikipress_url ); ?>"><?php echo $wikipress_label; ?></a>
                <?php endif; ?>
            </li>
            <?php if ( $wikipress_index < ( $wikipress_crumb_count - 1 ) ) : ?>
                <li class="breadcrumb-delimiter" aria-hidden="true"><?php echo esc_html( $wikipress_delimiter ); ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
