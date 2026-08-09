<?php
/**
 * TinyMCEHelper class for WikiPress plugin.
 *
 * @package TrilBDev\WikiPress
 * @subpackage Includes\Functions\Helpers
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Functions\Helpers;

final class TinyMCEHelper {
    /**
     * Render a TinyMCE editor.
     *
     * @param string $id            The ID of the editor.
     * @param string $name          The name attribute for the textarea.
     * @param string $label         The label for the editor.
     * @param string $value         The initial content of the editor.
     * @param int    $rows          The number of rows for the textarea.
     * @param bool   $media_buttons Whether to show media buttons.
     */
    public static function render( string $id, string $name, string $label, string $value = '', int $rows = 8, bool $media_buttons = false ): void {
        $editor_id = sanitize_key( $id );
        $editor_settings = [
            'textarea_name' => $name,
            'textarea_rows' => max( 1, $rows ),
            'media_buttons' => $media_buttons,
            'teeny' => false,
            'quicktags' => false,
            'wpautop' => true,
            'tinymce' => [
                'toolbar1' => 'undo redo | formatselect | bold italic underline | bullist numlist | link table | code',
                'toolbar2' => '',
                'statusbar' => true,
                'branding' => false,
            ],
        ];

        echo '<div class="wikipress-wp-editor">';
        printf( '<label class="form-label" for="%1$s">%2$s</label>', esc_attr( $editor_id ), esc_html( $label ) );
        if ( function_exists( 'wp_enqueue_editor' ) ) {
            wp_enqueue_editor();
        }
        if ( function_exists( 'wp_editor' ) ) {
            wp_editor( $value, $editor_id, $editor_settings );
        } else {
            printf( '<textarea class="form-control" id="%1$s" name="%2$s" rows="%3$d">%4$s</textarea>', esc_attr( $editor_id ), esc_attr( $name ), max( 1, $rows ), esc_textarea( $value ) );
        }
        echo '</div>';
    }
}