<?php

namespace TrilBDev\WikiPress\Includes\Functions\Helpers;

class TinyMCEHelper {
    private static bool $script_initialized = false;

    public static function render( string $id, string $name, string $label, string $value = '', int $rows = 8 ): void {
        wp_enqueue_script( 'wikipress-tinymce', WIKIPRESS_URL . 'vendor/tinymce/tinymce/tinymce.min.js', [], WIKIPRESS_VERSION, true );
        $config = wp_json_encode( [
            'selector' => '#' . $id,
            'menubar' => false,
            'branding' => false,
            'height' => max( 180, $rows * 32 ),
            'plugins' => 'lists link code table',
            'toolbar' => 'undo redo | blocks | bold italic underline | bullist numlist | link table | code',
            'statusbar' => true,
        ] );
        $textarea_label = esc_html( $label );
        if ( ! self::$script_initialized ) {
            wp_add_inline_script( 'wikipress-tinymce', "window.addEventListener('load',function(){if(window.tinymce){var root=window.wikipressShadowRoot||document;root.querySelectorAll('[data-wikipress-tinymce]').forEach(function(editor){var options=" . $config . ";options.target=editor;delete options.selector;tinymce.init(options);});}});" );
            self::$script_initialized = true;
        }
        printf( '<div class="form-floating"><textarea class="form-control" id="%1$s" name="%2$s" data-wikipress-tinymce placeholder="%3$s" rows="%4$d" style="height:auto">%5$s</textarea><label for="%1$s">%3$s</label></div>', esc_attr( $id ), esc_attr( $name ), $textarea_label, $rows, esc_textarea( $value ) );
    }

    public static function add_tinymce_plugin( $plugin_array ) {
        $plugin_array['wikipress'] = WIKIPRESS_URL . 'src/Assets/js/admin.tinymce.js';
        return $plugin_array;
    }

    public static function add_tinymce_button( $buttons ) {
        array_push( $buttons, 'wikipress' );
        return $buttons;
    }
}