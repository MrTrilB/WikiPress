<?php
/**
 * DebugManager class for WikiPress plugin.
 * 
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Admin\Manager\Tools;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class DebugManager {
    /**
     * Render debug-related settings fields.
     *
     * @param array<string, mixed> $values Current settings.
     * @return void
     */
    public function render( array $values ): void {
        $field_id = 'wikipress-debug-logging';
        $field = [
            'description' => __( 'Write diagnostic information to the WordPress debug log.', 'wikipress' ),
            'tooltip' => __( 'Enable this only while investigating a problem, because logs can grow over time.', 'wikipress' ),
            'tooltip_type' => 'info',
        ];
        echo '<tr><th scope="row">' . FormFieldHelper::label( $field_id, __( 'Debug logging', 'wikipress' ), $field ) . '</th><td>' . FormFieldHelper::checkbox( 'wikipress_tools[debug_logging]', '1', __( 'Enable WikiPress debug logging', 'wikipress' ), [ 'id' => $field_id, 'checked' => ! empty( $values['debug_logging'] ) ] ) . '</td></tr>';

        $field_id = 'wikipress-console-logging';
        $field = [
            'description' => __( 'Write diagnostic information to the browser console.', 'wikipress' ),
            'tooltip' => __( 'Use this during frontend troubleshooting and disable it afterward.', 'wikipress' ),
        ];
        echo '<tr><th scope="row">' . FormFieldHelper::label( $field_id, __( 'Console logging', 'wikipress' ), $field ) . '</th><td>' . FormFieldHelper::checkbox( 'wikipress_tools[console_logging]', '1', __( 'Enable browser console logging', 'wikipress' ), [ 'id' => $field_id, 'checked' => ! empty( $values['console_logging'] ) ] ) . '</td></tr>';
    }
}