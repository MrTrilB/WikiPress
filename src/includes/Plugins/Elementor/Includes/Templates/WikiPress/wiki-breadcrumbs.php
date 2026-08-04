<?php
/**
 * Docs Breadcrumbs widget template.
 *
 * @package TrilBDev
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

$crumbs          = isset( $context['crumbs'] ) && is_array( $context['crumbs'] ) ? $context['crumbs'] : [];
$delimiter       = isset( $context['delimiter'] ) ? (string) $context['delimiter'] : ' / ';
$wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'trilbdev-docs-breadcrumbs' ];
$aria_label      = isset( $context['nav_aria_label'] ) ? (string) $context['nav_aria_label'] : __( 'Breadcrumbs', 'trilbdev' );

if ( empty( $crumbs ) ) {
    return;
}

$sanitize_class = static function ( $class ) {
    $class = is_string( $class ) ? $class : '';

    $class = sanitize_html_class( $class );

    return '' !== $class ? $class : null;
};

$wrapper_class_attr = esc_attr( implode( ' ', array_filter( array_map( $sanitize_class, $wrapper_classes ) ) ) );
$delimiter_text     = esc_html( $delimiter );
$aria_label_attr    = esc_attr( $aria_label );

$crumb_count = count( $crumbs );
?>
<nav class="<?php echo $wrapper_class_attr; ?>" aria-label="<?php echo $aria_label_attr; ?>">
    <ol class="trilbdev-breadcrumb-list">
        <?php foreach ( $crumbs as $index => $crumb ) :
            if ( ! is_array( $crumb ) || empty( $crumb['label'] ) ) {
                continue;
            }

            $label      = esc_html( (string) $crumb['label'] );
            $url        = isset( $crumb['url'] ) ? (string) $crumb['url'] : '';
            $is_current = ! empty( $crumb['is_current'] );

            $item_classes = [ 'breadcrumb-item' ];
            if ( $is_current ) {
                $item_classes[] = 'is-current';
            }
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
                <?php if ( $is_current || '' === $url ) : ?>
                    <span class="breadcrumb-label"<?php echo $is_current ? ' aria-current="page"' : ''; ?>><?php echo $label; ?></span>
                <?php else : ?>
                    <a class="breadcrumb-link" href="<?php echo esc_url( $url ); ?>"><?php echo $label; ?></a>
                <?php endif; ?>
            </li>
            <?php if ( $index < ( $crumb_count - 1 ) ) : ?>
                <li class="breadcrumb-delimiter" aria-hidden="true"><?php echo $delimiter_text; ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
